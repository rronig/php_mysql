<?php session_start(); if(empty($_SESSION['username'])){ header("Location: register.php"); } ?>
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
            width: 500px;
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
            height: 50%;
            background: black;
            border: 2px solid black;
            z-index: 10;
        }
    </style>
</head>
<body>
    <h1>Tetris Game</h1>
    <div class="arcade-container">
        <img src="arcade.png" alt="Arcade Machine" class="arcade-image">
        <canvas id="gameCanvas" width="300" height="600"></canvas>
    </div>
    <p>Score: <span id="score">0</span></p>

    <script>
        const canvas = document.getElementById("gameCanvas");
        const ctx = canvas.getContext("2d");

        const box = 30;
        const rows = 20;
        const columns = 10;
        let score = 0;
        let gameInterval;
        let board = Array.from({ length: rows }, () => Array(columns).fill(0));
        let pieces = [
            [[1, 1, 1], [0, 1, 0]], // T shape
            [[1, 1], [1, 1]], // O shape
            [[0, 1, 1], [1, 1, 0]], // S shape
            [[1, 1, 0], [0, 1, 1]], // Z shape
            [[1, 1, 1, 1]], // I shape
            [[1, 0, 0], [1, 1, 1]], // L shape
            [[0, 0, 1], [1, 1, 1]] // J shape
        ];

        let currentPiece = generatePiece();
        let currentPos = { x: 4, y: 0 };

        document.addEventListener("keydown", movePiece);

        function generatePiece() {
            const randomIndex = Math.floor(Math.random() * pieces.length);
            return pieces[randomIndex];
        }

        function drawBoard() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Draw the grid
            for (let r = 0; r < rows; r++) {
                for (let c = 0; c < columns; c++) {
                    if (board[r][c] === 1) {
                        ctx.fillStyle = "red";
                        ctx.fillRect(c * box, r * box, box, box);
                    }
                }
            }

            // Draw current piece
            for (let r = 0; r < currentPiece.length; r++) {
                for (let c = 0; c < currentPiece[r].length; c++) {
                    if (currentPiece[r][c] === 1) {
                        ctx.fillStyle = "green";
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
            if (canMove(0, 1)) {
                currentPos.y++;
            } else {
                placePiece();
            }
            drawBoard();
        }

        function canMove(dx, dy, piece = currentPiece) {
            for (let r = 0; r < piece.length; r++) {
                for (let c = 0; c < piece[r].length; c++) {
                    if (piece[r][c] === 1) {
                        let newX = currentPos.x + c + dx;
                        let newY = currentPos.y + r + dy;
                        if (newX < 0 || newX >= columns || newY >= rows || board[newY] && board[newY][newX] === 1) {
                            return false;
                        }
                    }
                }
            }
            return true;
        }

        function placePiece() {
            for (let r = 0; r < currentPiece.length; r++) {
                for (let c = 0; c < currentPiece[r].length; c++) {
                    if (currentPiece[r][c] === 1) {
                        board[currentPos.y + r][currentPos.x + c] = 1;
                    }
                }
            }

            clearRows();
            currentPiece = generatePiece();
            currentPos = { x: 4, y: 0 };
            if (!canMove(0, 0)) {
                clearInterval(gameInterval);
                alert("Game Over! Your score: " + score);
                resetGame();
            }
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
            let rotatedPiece = rotateMatrix(currentPiece);
            let originalPos = { ...currentPos };
            if (canMove(0, 0, rotatedPiece)) {
                currentPiece = rotatedPiece;
            }
            drawBoard();
        }

        function rotateMatrix(matrix) {
            return matrix[0].map((_, index) => matrix.map(row => row[index])).reverse();
        }

        function resetGame() {
            board = Array.from({ length: rows }, () => Array(columns).fill(0));
            score = 0;
            document.getElementById("score").innerText = score;
            gameInterval = setInterval(autoDrop, 500);
        }

        function autoDrop() {
            moveDown();
        }

        gameInterval = setInterval(autoDrop, 500);  // Make blocks drop automatically every 500ms
    </script>
</body>
</html>
