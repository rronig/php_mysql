<?php session_start(); if(empty($_SESSION['username'])){
header("Location: register.php");
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Snake Game</title>
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
            height: 30%;
            background: blue;
            border: 2px solid black;
            z-index: 10;
        }
    </style>
</head>
<body>
    <h1>Snake Game</h1>
    <div class="arcade-container">
        <img src="arcade.png" alt="Arcade Machine" class="arcade-image">
        <canvas id="gameCanvas" width="400" height="400"></canvas>
    </div>
    <p>Score: <span id="score">0</span></p>

    <script>
        const canvas = document.getElementById("gameCanvas");
        const ctx = canvas.getContext("2d");

        const box = 20; 
        let snake = [{x: 10 * box, y: 10 * box}];
        let direction = "";
        let food = randomFood();
        let score = 0;
        let gameInterval;

        document.addEventListener("keydown", changeDirection);

        function changeDirection(event) {
            const key = event.keyCode;
            const goingUp = direction === "UP";
            const goingDown = direction === "DOWN";
            const goingLeft = direction === "LEFT";
            const goingRight = direction === "RIGHT";

            if ((key === 37 || key === 65) && !goingRight) direction = "LEFT";  // Left (A)
            if ((key === 38 || key === 87) && !goingDown) direction = "UP";    // Up (W)
            if ((key === 39 || key === 68) && !goingLeft) direction = "RIGHT";  // Right (D)
            if ((key === 40 || key === 83) && !goingUp) direction = "DOWN";    // Down (S)
        }

        function drawGame() {
            ctx.fillStyle = "blue";
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            ctx.fillStyle = "red";
            ctx.fillRect(food.x, food.y, box, box);

            ctx.fillStyle = "lime";
            snake.forEach(segment => ctx.fillRect(segment.x, segment.y, box, box));

            let newX = snake[0].x;
            let newY = snake[0].y;

            if (direction === "LEFT") newX -= box;
            if (direction === "UP") newY -= box;
            if (direction === "RIGHT") newX += box;
            if (direction === "DOWN") newY += box;

            let newHead = {x: newX, y: newY};

            if (collision(newHead)) {
                clearInterval(gameInterval);
                alert("Game Over! Your score: " + score);
                resetGame();
                return;
            }

            if (newX === food.x && newY === food.y) {
                score++;
                document.getElementById("score").innerText = score;
                food = randomFood();
            } else {
                snake.pop();
            }

            snake.unshift(newHead);
        }

        function collision(head) {
            // Proper boundary check
            if (head.x < -20 || head.y < -20 || head.x >= 420 || head.y >= 420) {
                console.warn("Boundary collision at:", head.x, head.y);
                return true;
            }

            // Check if the snake collides with itself
            for (let i = 1; i < snake.length; i++) {
                if (head.x === snake[i].x && head.y === snake[i].y) {
                    console.warn("Self-collision detected at:", head.x, head.y);
                    return true;
                }
            }

            return false;
        }

        function randomFood() {
            return {
                x: Math.floor(Math.random() * (canvas.width / box)) * box,
                y: Math.floor(Math.random() * (canvas.height / box)) * box
            };
        }

        function resetGame() {
            snake = [{x: 10 * box, y: 10 * box}];
            direction = "";
            food = randomFood();
            score = 0;
            document.getElementById("score").innerText = score;
            gameInterval = setInterval(drawGame, 100);
        }

        gameInterval = setInterval(drawGame, 100);
    </script>
</body>
</html>
