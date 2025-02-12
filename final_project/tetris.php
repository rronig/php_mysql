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
    <style>
        body { 
            text-align: center; 
            background: black; 
            color: white;
            font-family: Arial, sans-serif;
        }
        .arcade-container {
            position: relative;
            width: 650px;
            margin: auto;
        }
        .arcade-image {
            width: 100%;
            display: block;
        }
        #gameCanvas {
            position: absolute;
            top: 24%;
            left: 19%;
            width: 60%;
            height: 30%;
            background: gray;
            border: 2px solid black;
            z-index: 10;
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
    <h1>Tetris Game</h1>
    <div class="arcade-container">
        <img src="arcade.png" alt="Arcade Machine" class="arcade-image">
        <canvas id="gameCanvas" width="300" height="600"></canvas>
    </div>
    <p>Score: <span id="score">0</span></p>
    <p>High Score: <span id="high-score"><?php echo $high_score ?? '0'; ?></span></p>
    <h1><a href="snake.php" style="background-color:white;text-decoration:none;padding:5px;border:solid white;">Also Try Snake!</a></h1>
    <script>
        const canvas = document.getElementById("gameCanvas");
        const ctx = canvas.getContext("2d");

        const box = 30;
        const rows = 20;
        const columns = 10;
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
        let currentPieceObj = generatePiece();
        let currentPos = { x: 4, y: 0 };
        document.addEventListener("keydown", movePiece);
        function generatePiece() {
            let randomIndex = Math.floor(Math.random() * pieces.length);
            return {
                shape: pieces[randomIndex],
                color: colors[randomIndex]
            };
        }
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
                if (board[r].every(cell => cell === 1)) {
                    board.splice(r, 1);
                    board.unshift(Array(columns).fill(0));
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
            board = Array.from({ length: rows }, () => Array(columns).fill(0));
            score = 0;
            document.getElementById("score").innerText = score;
            gameInterval = setInterval(autoDrop, 100);
            saveScore(score); // Save the score when the game resets
        }

        // Modify the game over logic to save the score
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
            clearRows();
            currentPieceObj = generatePiece();
            currentPos = { x: 4, y: 0 };
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
