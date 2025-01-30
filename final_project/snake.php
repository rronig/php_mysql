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
            position: relative; /* Makes it a reference for absolute positioning */
            width: 500px; /* Adjust based on your arcade machine image */
            margin: auto;
        }
        .arcade-image {
            width: 100%; /* Makes the image fit inside the container */
            display: block;
        }
        #gameCanvas {
            position: absolute;
            top: 24%;  /* Adjust to fit inside the arcade screen */
            left: 19%; /* Adjust to fit inside the arcade screen */
            width: 60%;
            height: 30%;
            background: blue;
            border: 10px solid black;
            z-index: 10;
        }
    </style>
</head>
<body>
    <h1>Snake Game</h1>
    <div class="arcade-container">
        <img src="arcade.png" alt="Arcade Machine" class="arcade-image">
        <canvas id="gameCanvas" width="300" height="300"></canvas>
    </div>
    <p>Score: <span id="score">0</span></p>

    <script>
        const canvas = document.getElementById("gameCanvas");
        const ctx = canvas.getContext("2d");

        const box = 20; 
        let snake = [{x: 10 * box, y: 10 * box}];
        let direction = "RIGHT";
        let food = {
            x: Math.floor(Math.random() * (canvas.width / box)) * box,
            y: Math.floor(Math.random() * (canvas.height / box)) * box
        };
        let score = 0;

        document.addEventListener("keydown", changeDirection);
        
        function changeDirection(event) {
            const key = event.keyCode;
            if (key === 37 && direction !== "RIGHT") direction = "LEFT";
            if (key === 38 && direction !== "DOWN") direction = "UP";
            if (key === 39 && direction !== "LEFT") direction = "RIGHT";
            if (key === 40 && direction !== "UP") direction = "DOWN";
        }

        function drawGame() {
            ctx.fillStyle = "blue";
            ctx.fillRect(0, 0, canvas.width, canvas.height);

            // Draw food
            ctx.fillStyle = "red";
            ctx.fillRect(food.x, food.y, box, box);

            // Draw snake
            ctx.fillStyle = "lime";
            snake.forEach(segment => ctx.fillRect(segment.x, segment.y, box, box));

            let newX = snake[0].x;
            let newY = snake[0].y;

            if (direction === "LEFT") newX -= box;
            if (direction === "UP") newY -= box;
            if (direction === "RIGHT") newX += box;
            if (direction === "DOWN") newY += box;

            // Check for collision with food
            if (newX === food.x && newY === food.y) {
                score++;
                document.getElementById("score").innerText = score;
                food = {
                    x: Math.floor(Math.random() * (canvas.width / box)) * box,
                    y: Math.floor(Math.random() * (canvas.height / box)) * box
                };
                saveScore(score);
            } else {
                snake.pop();
            }

            let newHead = {x: newX, y: newY};

            // Check for collisions
            if (collision(newHead)) {
                alert("Game Over! Your score: " + score);
                window.location.reload();
            }

            snake.unshift(newHead);
        }

        function collision(head) {
            return snake.some((segment, index) => index !== 0 && head.x === segment.x && head.y === segment.y)
                || head.x < 0 || head.y < 0 || head.x >= canvas.width || head.y >= canvas.height;
        }

        setInterval(drawGame, 100);
    </script>
</body>
</html>
