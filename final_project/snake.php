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
        <canvas id="gameCanvas" width="300" height="300"></canvas>
    </div>
    <p>Score: <span id="score">0</span></p>

    <script>
        const canvas = document.getElementById("gameCanvas");
        const ctx = canvas.getContext("2d");

        const box = 20; 
        let snake = [{x: 10 * box, y: 10 * box}];
        let direction = "";
        let food = {
            x: Math.floor(Math.random() * (canvas.width / box)) * box,
            y: Math.floor(Math.random() * (canvas.height / box)) * box
        };
        let score = 0;
        let changingDirection = false;  // Flag to prevent rapid direction changes

        document.addEventListener("keydown", changeDirection);

        function changeDirection(event) {
            if (changingDirection) return; // Prevent direction change if one is already in progress
            changingDirection = true;

            const key = event.keyCode;
            if (key === 37 && direction !== "RIGHT" || key===65 && direction !== "RIGHT") direction = "LEFT";
            if (key === 38 && direction !== "DOWN" || key===87 && direction !== "DOWN") direction = "UP";
            if (key === 39 && direction !== "LEFT" || key===68 && direction !== "LEFT") direction = "RIGHT";
            if (key === 40 && direction !== "UP" || key===83 && direction !== "UP") direction = "DOWN";

            // Reset changingDirection flag after a brief delay to allow next valid key press
            setTimeout(() => {
                changingDirection = false;
            }, 100);  // 100ms delay between changes
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

            if (newX === food.x && newY === food.y) {
                score++;
                document.getElementById("score").innerText = score;
                food = {
                    x: Math.floor(Math.random() * (canvas.width / box)) * box,
                    y: Math.floor(Math.random() * (canvas.height / box)) * box
                };
            } else {
                snake.pop();
            }

            let newHead = {x: newX, y: newY};

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
