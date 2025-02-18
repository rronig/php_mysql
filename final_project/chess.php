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
    <!-- Chessboard.js CSS -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/chessboard-js/1.0.0/chessboard-1.0.0.min.css">
    <style>
        body {
            text-align: center;
    background: linear-gradient(135deg, #66785F, white);
    background-attachment: fixed; /* Ensures the gradient covers the entire page */
            color: white;
            font-family: Arial, sans-serif;
        }
        #chessboard {
            width: 400px;
            margin: 20px auto;
        }
        .logout-button {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            background: #4B5945;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 16px;
        }
        .logout-button:hover {
            background: #66785F;
        }
    </style>
</head>
<body>
    <!-- Logout Button -->
    <form action="logout.php" method="POST">
        <button type="submit" class="logout-button">Logout</button>
    </form>

    <h1>Chess Game</h1>
    <div id="chessboard"></div>
    <p id="status">Make your move!</p>
    <!-- Add jQuery -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.6.0/jquery.min.js"></script>
    <!-- Chessboard.js JavaScript -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chessboard-js/1.0.0/chessboard-1.0.0.min.js"></script>
    <!-- Chess.js for move validation -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/chess.js/0.10.2/chess.min.js"></script>
    
    <script>
    // Initialize the chessboard
    const board = Chessboard('chessboard', {
        draggable: true,
        dropOffBoard: 'trash',
        sparePieces: false,
        position: 'start',
        onDrop: handleMove
    });

    // Initialize the chess game
    const game = new Chess();

    // Initialize Stockfish Web Worker (use a proper worker script)
    const stockfish = new Worker('stockfish.js');

// Handle Stockfish responses
stockfish.onmessage = function (event) {
    const response = event.data;
    console.log('Stockfish response:', response); // Log all responses for debugging

    // Only act on the "bestmove" response
    if (response.startsWith('bestmove')) {
        const bestMove = response.split(' ')[1]; // Extract the best move (e.g., "e7e6")
        console.log('Best move:', bestMove);

        // Make the move on the board
        const move = game.move({ from: bestMove.substring(0, 2), to: bestMove.substring(2, 4) });

        if (move === null) {
            console.error('Invalid move from Stockfish:', bestMove);
            return;
        }

        // Update the board position
        board.position(game.fen());

        // Update the game status
        updateStatus();

        // Stop Stockfish from calculating further
        stockfish.postMessage('stop'); // Stop Stockfish after making the move
    }
};

    stockfish.onerror = function(error) {
        console.log('Stockfish Web Worker error:', error); // Log any errors from the worker
    };

    stockfish.postMessage('uci'); // Initialize Stockfish
    stockfish.postMessage('ucinewgame'); // Start a new game with Stockfish

    // Handle user moves
    function handleMove(source, target) {
    const move = game.move({ from: source, to: target, promotion: 'q' }); // Always promote to queen for simplicity

    if (move === null) {
        return 'snapback'; // Illegal move
    }

    // Update the board and status
    board.position(game.fen());
    updateStatus();

    // Send the updated position to Stockfish
    stockfish.postMessage(`position fen ${game.fen()}`);

    // Ask Stockfish to calculate its move
    stockfish.postMessage('go depth 15');
}

    // Function to handle Stockfish's response
    function handleEngineResponse(event) {
        const response = event.data;
        console.log('Handling response:', response); // Log the response

        if (response.startsWith('bestmove')) {
            const bestMove = response.split(' ')[1];
            const move = game.move(bestMove);  // Make the move with Stockfish's recommendation
            board.position(game.fen());  // Update the board with the new position
            updateStatus();  // Update the game status

            // Only send the updated position to Stockfish if the game isn't over
            if (!game.game_over()) {
                stockfish.postMessage(`position fen ${game.fen()}`);
                stockfish.postMessage('go depth 15');  // Request the next move from Stockfish if the game isn't over
            } else {
                console.log("Game over!");
                stockfish.postMessage('quit'); // Stop Stockfish once the game is over
            }
        }
    }

    function updateStatus() {
    let status = '';

    if (game.in_checkmate()) {
        status = 'Game over, checkmate!';
    } else if (game.in_draw()) {
        status = 'Game over, draw!';
    } else {
        status = game.turn() === 'w' ? 'White to move' : 'Black to move';
        if (game.in_check()) {
            status += ', check!';
        }
    }

    document.getElementById('status').textContent = status;
}

    // Start the game
    updateStatus();
</script>



</body>
</html>
