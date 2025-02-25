<?php
session_start();

// Redirect to login if the user is not logged in
if (empty($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Chess Game</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Rubik+Dirt&family=Rubik+Spray+Paint&family=Sigmar&display=swap" rel="stylesheet">
    <!-- Chessboard.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chessboard-js/1.0.0/chessboard-1.0.0.min.css">
    <style>
        body {
            text-align: center;
            background: linear-gradient(87deg, rgb(87, 143, 202), rgb(209, 248, 239));
            background-attachment: fixed;
            color: white;
            font-family: Arial, sans-serif;
        }
        #chessboard {
            width: 800px;
            margin: 20px auto;
        }
        .logout-button, .dashboard-button {
            position: absolute;
            top: 10px;
            padding: 10px 20px;
            background: #3673b5;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 32px;
        }
        .logout-button { right: 10px; }
        .dashboard-button { left: 10px; }
        .logout-button:hover, .dashboard-button:hover { background: #5C8FC7; }
        .sigmar-regular { font-family: "Sigmar", serif; font-size: 64px; }
        .rubik-dirt-regular { font-family: "Rubik Dirt", serif; font-size: 32px; }
        .rubik-spray-paint-regular { font-family: "Rubik Spray Paint", serif; }
        nav { background: blue; }

        /* Promotion Popup */
        #promotion-popup {
            display: none;
            position: fixed;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            background: white;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0px 4px 10px rgba(0, 0, 0, 0.3);
            text-align: center;
        }
        #promotion-popup p { color: black; font-size: 20px; }
        #promotion-popup button {
            font-size: 20px;
            margin: 5px;
            padding: 10px;
            cursor: pointer;
            border: none;
            border-radius: 5px;
            background: #3673b5;
            color: white;
        }
        #promotion-popup button:hover { background: #5C8FC7; }
    </style>
</head>
<body>
    <nav>
        <form action="logout.php" method="POST">
            <button type="submit" class="logout-button rubik-spray-paint-regular">Logout</button>
        </form>
        <form action="dashboard.php" method="POST">
            <button type="submit" class="dashboard-button rubik-spray-paint-regular">Dashboard</button>
        </form>
    </nav>
    <h1 class="sigmar-regular">Chess Game</h1>
    <div id="chessboard"></div>

    <label for="difficulty" class="rubik-dirt-regular">Stockfish Difficulty:</label>
    <input type="range" id="difficulty" min="1" max="20" value="10" oninput="updateDifficulty(this.value)">
    <span id="difficulty-value" class="rubik-dirt-regular">10</span>

    <h2 class="rubik-dirt-regular" id="status">Make your move!</h2>

    <!-- Promotion Selection Popup -->
    <div id="promotion-popup">
        <p>Choose a promotion:</p>
        <button onclick="setPromotion('q')">♛ Queen</button>
        <button onclick="setPromotion('r')">♜ Rook</button>
        <button onclick="setPromotion('b')">♝ Bishop</button>
        <button onclick="setPromotion('n')">♞ Knight</button>
    </div>

    <!-- Add jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Chessboard.js JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chessboard-js/1.0.0/chessboard-1.0.0.min.js"></script>
    <!-- Chess.js for move validation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chess.js/0.10.2/chess.min.js"></script>

    <script>
        let board, game = new Chess();
        let stockfish = new Worker('stockfish.js');
        let pendingPromotion = null;
        let playerTurn = true; // Ensure the player can only move when it's their turn

        function updateDifficulty(value) {
            document.getElementById("difficulty-value").textContent = value;
            stockfish.postMessage(`setoption name Skill Level value ${value}`);
        }


        board = Chessboard('chessboard', {
            draggable: true,
            position: 'start',
            onDragStart: (source, piece) => {
                if (!playerTurn || (game.turn() === 'w' && piece.startsWith('b')) || (game.turn() === 'b' && piece.startsWith('w'))) {
                    return false; // Prevent moving opponent's pieces
                }
            },
            onDrop: handleMove
        });

        function handleMove(source, target) {
            if (!playerTurn) return 'snapback';

            let moveObj = { from: source, to: target };

            // Handle pawn promotion
            if (game.get(source).type === 'p' && (target[1] === '1' || target[1] === '8')) {
                pendingPromotion = { source, target };
                document.getElementById('promotion-popup').style.display = 'block';
                return;
            }

            // Check if the move is legal before making it
            const move = game.move(moveObj);
            if (!move) return 'snapback';

            board.position(game.fen());
            updateStatus();
            playerTurn = false; // Lock player movement

            if (!game.game_over()) {
                setTimeout(() => {
                    stockfish.postMessage(`position fen ${game.fen()}`);
                    stockfish.postMessage('go depth 15');
                }, 200);
            }
        }


        function setPromotion(piece) {
            if (!pendingPromotion) return;
            const { source, target } = pendingPromotion;
            pendingPromotion = null;
            document.getElementById('promotion-popup').style.display = 'none';
            makeMove({ from: source, to: target, promotion: piece });
        }

        function makeMove(moveObj) {
            if (!playerTurn) return;

            const move = game.move(moveObj);
            if (!move) return 'snapback';

            board.position(game.fen());
            updateStatus();
            playerTurn = false; // Lock player movement

            if (!game.game_over()) {
                setTimeout(() => {
                    stockfish.postMessage(`position fen ${game.fen()}`);
                    stockfish.postMessage('go depth 15');
                }, 200);
            }
        }

        stockfish.onmessage = function (event) {
            if (playerTurn) return; // Stockfish only moves when it's its turn

            if (event.data.startsWith('bestmove')) {
                const bestMove = event.data.split(' ')[1];

                if (bestMove !== '(none)') { // Ensure Stockfish actually has a move
                    game.move({ from: bestMove.substring(0, 2), to: bestMove.substring(2, 4), promotion: bestMove.substring(4, 5) });
                    board.position(game.fen());
                }

                updateStatus();
                playerTurn = true; // Allow the player to move again
            }
        };

        function updateStatus() {
            let status = '';
            if (game.game_over()) {
                // If the game is over, display the result (checkmate or draw)
                saveHighScore(document.getElementById("difficulty").value);
                status = game.in_checkmate() ? 'Checkmate!' :
                        game.in_draw() ? 'Draw!' : 'Game Over';
            } else {
                // Otherwise, show the current turn
                status = game.turn() === 'w' ? 'White to move' : 'Black to move';
                if (game.in_check()) {
                    status += ', check!';
                }
            }

            document.getElementById('status').textContent = status;
        }
        function saveHighScore(difficulty) {
            $.ajax({
                url: 'savescore.php',
                type: 'POST',
                data: {
                    difficulty: difficulty
                },
                success: function(response) {
                    console.log('Score saved successfully');
                },
                error: function(xhr, status, error) {
                    console.log('Error saving score: ' + error);
                }
            });
        }


        stockfish.postMessage('uci');
        stockfish.postMessage('ucinewgame');
        updateStatus();
    </script>
</body>
</html>
