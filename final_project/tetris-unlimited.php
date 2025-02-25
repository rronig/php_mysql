<?php
session_start();
if (empty($_SESSION['username'])) {
    header("Location: register.php");
}

// Database connection
$host = "127.0.0.1";
$dbUsername = "root"; // Replace with your MySQL username
$dbPassword = ""; // Replace with your MySQL password
$dbName = "final_project_php_mysql";

$conn = new mysqli($host, $dbUsername, $dbPassword, $dbName);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch the user's high score for Tetris
$user_id = $_SESSION['user_id'];
$sql = "SELECT tetris FROM scores WHERE user_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$stmt->bind_result($high_score);
$stmt->fetch();
$stmt->close();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tetris Game</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Rubik+Dirt&family=Rubik+Spray+Paint&family=Sigmar&display=swap" rel="stylesheet">
     <style>
        body { 
            text-align: center; 
            background: linear-gradient(87deg, rgb(87, 143, 202), rgb(209, 248, 239));
            background-attachment: fixed; /* Ensures the gradient covers the entire page */
            color: white;
            font-family: Arial, sans-serif;
        }
        #gameCanvas {
            background: gray;
            border: 2px solid black;
            z-index: 10;
        }
        .logout-button {
            position: absolute;
            top: 10px;
            right: 10px;
            padding: 10px 20px;
            background: #3673b5;
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 32px;
        }
        .dashboard-button {
            position: absolute;
            top: 10px;
            left: 10px;
            padding: 10px 20px;
            background: rgb(54, 116, 181);
            color: white;
            border: none;
            border-radius: 5px;
            cursor: pointer;
            font-size: 32px;
        }
        .logout-button:hover {
            background: #5C8FC7;
        }
        .dashboard-button:hover {
            background: #5C8FC7;
        }
        .sigmar-regular {
            font-family: "Sigmar", serif;
            font-weight: 400;
            font-style: normal;
            font-size: 64px;
        }
        .rubik-dirt-regular {
            font-family: "Rubik Dirt", serif;
            font-weight: 400;
            font-style: normal;
            font-size:32px;
        }
        .rubik-spray-paint-regular {
            font-family: "Rubik Spray Paint", serif;
            font-weight: 400;
            font-style: normal;
        }
    </style>
</head>
<body>
    <!-- Logout Button -->
    <form action="logout.php" method="POST">
        <button type="submit" class="logout-button rubik-spray-paint-regular">Logout</button>
    </form>
    <form action="dashboard.php" method="POST">
        <button type="submit" class="dashboard-button rubik-spray-paint-regular">Dashboard</button>
    </form>
    <h1 class="sigmar-regular">Tetris Unlimited</h1>
    <canvas id="gameCanvas" width="900" height="1200"></canvas>
    <h1 class="rubik-dirt-regular">Score: <span id="score">0</span></h1>
    <h1 class="rubik-dirt-regular">High Score: <span id="high-score"><?php echo $high_score ?? '0'; ?></span></h1>
    <script>
        const canvas = document.getElementById("gameCanvas");
        const ctx = canvas.getContext("2d");

        const box = 30;
        const rows = 40;
        const columns = 30;
        let score = 0;
        let gameInterval;
        let board = Array.from({ length: rows }, () => Array(columns).fill(null));
        let pieces = [
            [[1, 1, 1], [0, 1, 0]], // T shape
            [[1, 1], [1, 1]], // O shape
            [[0, 1, 1], [1, 1, 0]], // S shape
            [[1, 1, 0], [0, 1, 1]], // Z shape
            [[1, 1, 1, 1]], // I shape
            [[1, 0, 0], [1, 1, 1]], // L shape
            [[0, 0, 1], [1, 1, 1]] // J shape
        ];

        const colors = ["#800080", "#ffff00", "#00ff00", "#ff0000", "#00ffff", "#ff7f00", "#0000ff"];

        function generatePiece() {
            let randomIndex = Math.floor(Math.random() * pieces.length);
            const piece = {
                shape: pieces[randomIndex],
                color: colors[randomIndex]
            };

            // Calculate the center position for the piece
            const pieceWidth = piece.shape[0].length;
            const centerX = Math.floor((columns - pieceWidth) / 2);

            return piece;
        }

        let currentPieceObj = generatePiece();
        let currentPos = { x: Math.floor((columns - currentPieceObj.shape[0].length) / 2), y: 0 };

        document.addEventListener("keydown", movePiece);

        function drawBoard() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            for (let r = 0; r < rows; r++) {
                for (let c = 0; c < columns; c++) {
                    if (board[r][c]) {
                        ctx.fillStyle = board[r][c]; // Use stored color
                        ctx.fillRect(c * box, r * box, box, box);
                    }
                }
            }

            // Draw current piece with its color
            ctx.fillStyle = currentPieceObj.color;
            for (let r = 0; r < currentPieceObj.shape.length; r++) {
                for (let c = 0; c < currentPieceObj.shape[r].length; c++) {
                    if (currentPieceObj.shape[r][c] === 1) {
                        ctx.fillRect((currentPos.x + c) * box, (currentPos.y + r) * box, box, box);
                    }
                }
            }
        }

        function movePiece(event) {
            if (event.keyCode === 37) moveLeft(); // Left arrow
            if (event.keyCode === 39) moveRight(); // Right arrow
            if (event.keyCode === 40) moveDown(); // Down arrow
            if (event.keyCode === 38) rotatePiece(); // Up arrow
        }

        function moveLeft() {
            if (canMove(-1, 0)) currentPos.x--;
            drawBoard();
        }

        function moveRight() {
            if (canMove(1, 0)) currentPos.x++;
            drawBoard();
        }

        function moveDown() {
            if (canMove(0, 1, currentPieceObj.shape)) {
                currentPos.y++;
            } else {
                placePiece();
            }
            drawBoard();
        }

        function canMove(dx, dy, piece = currentPieceObj.shape) {
            for (let r = 0; r < piece.length; r++) {
                for (let c = 0; c < piece[r].length; c++) {
                    if (piece[r][c] === 1) {
                        let newX = currentPos.x + c + dx;
                        let newY = currentPos.y + r + dy;
                        if (newX < 0 || newX >= columns || newY >= rows || (board[newY] && board[newY][newX])) {
                            return false;
                        }
                    }
                }
            }
            return true;
        }

        function clearRows() {
            for (let r = rows - 1; r >= 0; r--) {
                // Check if every cell in the row is filled (not null)
                if (board[r].every(cell => cell !== null)) {
                    // Remove the completed row
                    board.splice(r, 1);
                    // Add a new empty row at the top
                    board.unshift(Array(columns).fill(null));
                    // Increase the score
                    score += 100;
                    document.getElementById("score").innerText = score;
                }
            }
        }

        function rotatePiece() {
            let rotatedPiece = rotateMatrix(currentPieceObj.shape);
            let originalPos = { ...currentPos };
            if (canMove(0, 0, rotatedPiece)) {
                currentPieceObj.shape = rotatedPiece;
            }
            drawBoard();
        }

        function rotateMatrix(matrix) {
            return matrix[0].map((_, index) => matrix.map(row => row[index])).reverse();
        }

        function saveScore(score) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "save_tetris_score.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log("Score saved successfully!");
                }
            };
            xhr.send("score=" + score);
        }

        function resetGame() {
            board = Array.from({ length: rows }, () => Array(columns).fill(null));
            score = 0;
            document.getElementById("score").innerText = score;
            gameInterval = setInterval(autoDrop, 100);
            saveScore(score); // Save the score when the game resets
        }

        function placePiece() {
            for (let r = 0; r < currentPieceObj.shape.length; r++) {
                for (let c = 0; c < currentPieceObj.shape[r].length; c++) {
                    if (currentPieceObj.shape[r][c] === 1) {
                        board[currentPos.y + r][currentPos.x + c] = currentPieceObj.color;
                    }
                }
            }
            score += 10;
            document.getElementById("score").innerText = score;
            clearRows(); // Clear completed rows after placing the piece
            currentPieceObj = generatePiece();
            currentPos = { x: Math.floor((columns - currentPieceObj.shape[0].length) / 2), y: 0 };
            if (!canMove(0, 0)) {
                clearInterval(gameInterval);
                alert("Game Over! Your score: " + score);
                saveScore(score); // Save the score when the game ends
                resetGame();
            }
        }

        function autoDrop() {
            moveDown();
        }

        gameInterval = setInterval(autoDrop, 100);  // Make blocks drop automatically every 500ms
    </script>
</body>
</html>
