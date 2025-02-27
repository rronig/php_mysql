/*
  Stockfish, a UCI chess playing engine derived from Glaurung 2.1
  Copyright (C) 2004-2008 Tord Romstad (Glaurung author)
  Copyright (C) 2008-2015 Marco Costalba, Joona Kiiski, Tord Romstad
  Copyright (C) 2015-2020 Marco Costalba, Joona Kiiski, Gary Linscott, Tord Romstad

  Stockfish is free software: you can redistribute it and/or modify
  it under the terms of the GNU General Public License as published by
  the Free Software Foundation, either version 3 of the License, or
  (at your option) any later version.

  Stockfish is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

#include <cassert>

#include <algorithm> // For std::count
#include "movegen.h"
#include "search.h"
#include "thread.h"
#include "uci.h"
#include "tt.h"

#ifndef _WIN32
void* run_idle_loop(void* thread) {
  static_cast<Thread*>(thread)->idle_loop();
  return nullptr;
}
#endif

ThreadPool Threads; // Global object


/// Thread constructor launches the thread and waits until it goes to sleep
/// in idle_loop(). Note that 'searching' and 'exit' should be already set.

#ifdef _WIN32
Thread::Thread(size_t n) : idx(n), stdThread(&Thread::idle_loop, this) {
#else
Thread::Thread(size_t n) : idx(n) {
#endif

#ifndef _WIN32
  // With increased MAX_MOVES (for variants) the stack can grow larger than the
  // system default. Explicitly set a sufficient stack size.
  pthread_attr_t attr;
  pthread_attr_init(&attr);
  pthread_attr_setstacksize(&attr, 4096 * MAX_MOVES);
  pthread_create(&nativeThread, &attr, run_idle_loop, this);
#endif

  // (A) Upstream does wait_for_search_finished() directly here.
  //
  // This deadlocks with emscripten: We are waiting for the newly created
  // thread to set the condition variable before we yield to the browser. But
  // we need to yield to the browser to create the worker for the newly created
  // thread.
  //
  // https://bugzilla.mozilla.org/show_bug.cgi?id=1049079
  //
  // Instead we introduced threadStarted (B) and retry uci_command with
  // exponential backoff until all threads have started.
}


/// Thread destructor wakes up the thread in idle_loop() and waits
/// for its termination. Thread should be already waiting.

Thread::~Thread() {

  assert(!searching);

  exit = true;
  start_searching();
#ifdef _WIN32
  stdThread.join();
#else
  pthread_join(nativeThread, nullptr);
#endif
}


/// Thread::bestMoveCount(Move move) return best move counter for the given root move

int Thread::best_move_count(Move move) const {

  auto rm = std::find(rootMoves.begin() + pvIdx,
                      rootMoves.begin() + pvLast, move);

  return rm != rootMoves.begin() + pvLast ? rm->bestMoveCount : 0;
}


/// Thread::clear() reset histories, usually before a new game

void Thread::clear() {

  counterMoves.fill(MOVE_NONE);
  mainHistory.fill(0);
  lowPlyHistory.fill(0);
  captureHistory.fill(0);

  for (bool inCheck : { false, true })
      for (StatsType c : { NoCaptures, Captures })
      {
          for (auto& to : continuationHistory[inCheck][c])
                for (auto& h : to)
                      h->fill(0);
          continuationHistory[inCheck][c][NO_PIECE][0]->fill(Search::CounterMovePruneThreshold - 1);
      }
}


/// Thread::start_searching() wakes up the thread that will start the search

void Thread::start_searching() {

  std::lock_guard<std::mutex> lk(mutex);
  searching = true;
  cv.notify_one(); // Wake up the thread in idle_loop()
}


/// Thread::wait_for_search_finished() blocks on the condition variable
/// until the thread has finished searching.

void Thread::wait_for_search_finished() {

  std::unique_lock<std::mutex> lk(mutex);
  cv.wait(lk, [&]{ return !searching; });
}


/// Thread::idle_loop() is where the thread is parked, blocked on the
/// condition variable, when it has no work to do.

void Thread::idle_loop() {

  while (true)
  {
      std::unique_lock<std::mutex> lk(mutex);
      searching = false;
      threadStarted = true; // (B)

      cv.notify_one(); // Wake up anyone waiting for search finished
      cv.wait(lk, [&]{ return searching; });

      if (exit)
          return;

      lk.unlock();

      search();
  }
}

/// ThreadPool::set() creates/destroys threads to match the requested number.
/// Created and launched threads will immediately go to sleep in idle_loop.
/// Upon resizing, threads are recreated to allow for binding if necessary.
///
/// stockfish.wasm: Unlike upstream, we reuse existing threads, because
/// we do not care about thread binding. For the same reason, we also do not
/// reallocate the transposition table.

void ThreadPool::set(size_t requested) {

  if (size() == requested)
      return;

  if (size() > 0) {
      main()->wait_for_search_finished();

      while (size() > requested)
          delete back(), pop_back();
  }

  if (requested > 0) {
      while (size() < requested)
          push_back(size() ? new Thread(size()) : new MainThread(0));
      clear();

      // Init thread number dependent search params.
      Search::init();
  }
}


/// ThreadPool::clear() sets threadPool data to initial values

void ThreadPool::clear() {

  for (Thread* th : *this)
      th->clear();

  main()->callsCnt = 0;
  main()->bestPreviousScore = VALUE_INFINITE;
  main()->previousTimeReduction = 1.0;
}


/// ThreadPool::start_thinking() wakes up main thread waiting in idle_loop() and
/// returns immediately. Main thread will wake up other threads and start the search.

void ThreadPool::start_thinking(Position& pos, StateListPtr& states,
                                const Search::LimitsType& limits, bool ponderMode) {

  main()->wait_for_search_finished();

  main()->stopOnPonderhit = stop = false;
  increaseDepth = true;
  main()->ponder = ponderMode;
  Search::Limits = limits;
  Search::RootMoves rootMoves;

  for (const auto& m : MoveList<LEGAL>(pos))
      if (   limits.searchmoves.empty()
          || std::count(limits.searchmoves.begin(), limits.searchmoves.end(), m))
          rootMoves.emplace_back(m);

  // After ownership transfer 'states' becomes empty, so if we stop the search
  // and call 'go' again without setting a new position states.get() == NULL.
  assert(states.get() || setupStates.get());

  if (states.get())
      setupStates = std::move(states); // Ownership transfer, states is now empty

  // We use Position::set() to set root position across threads. But there are
  // some StateInfo fields (previous, pliesFromNull, capturedPiece) that cannot
  // be deduced from a fen string, so set() clears them and to not lose the info
  // we need to backup and later restore setupStates->back(). Note that setupStates
  // is shared by threads but is accessed in read-only mode.
  StateInfo tmp = setupStates->back();

  for (Thread* th : *this)
  {
      th->nodes = th->nmpMinPly = th->bestMoveChanges = 0;
      th->rootDepth = th->completedDepth = 0;
      th->rootMoves = rootMoves;
      th->rootPos.set(pos.fen(), pos.is_chess960(), pos.subvariant(), &setupStates->back(), th);
  }

  setupStates->back() = tmp;

  main()->start_searching();
}

Thread* ThreadPool::get_best_thread() const {

    Thread* bestThread = front();
    std::map<Move, int64_t> votes;
    Value minScore = VALUE_NONE;

    // Find minimum score of all threads
    for (Thread* th: *this)
        minScore = std::min(minScore, th->rootMoves[0].score);

    // Vote according to score and depth, and select the best thread
    for (Thread* th : *this)
    {
        votes[th->rootMoves[0].pv[0]] +=
            (th->rootMoves[0].score - minScore + 14) * int(th->completedDepth);

          if (abs(bestThread->rootMoves[0].score) >= VALUE_TB_WIN_IN_MAX_PLY)
          {
              // Make sure we pick the shortest mate / TB conversion or stave off mate the longest
              if (th->rootMoves[0].score > bestThread->rootMoves[0].score)
                  bestThread = th;
          }
          else if (   th->rootMoves[0].score >= VALUE_TB_WIN_IN_MAX_PLY
                   || (   th->rootMoves[0].score > VALUE_TB_LOSS_IN_MAX_PLY
                       && votes[th->rootMoves[0].pv[0]] > votes[bestThread->rootMoves[0].pv[0]]))
              bestThread = th;
    }

    return bestThread;
}


/// Start non-main threads

void ThreadPool::start_searching() {

    for (Thread* th : *this)
        if (th != front())
            th->start_searching();
}


/// Wait for non-main threads

void ThreadPool::wait_for_search_finished() const {

    for (Thread* th : *this)
        if (th != front())
            th->wait_for_search_finished();
}
