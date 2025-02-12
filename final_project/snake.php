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

// Fetch the user's high score
$user_id = $_SESSION['user_id'];
$sql = "SELECT snake FROM scores WHERE user_id = ?";
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

    <h1>Snake Game</h1>
    <div class="arcade-container">
        <img src="arcade.png" alt="Arcade Machine" class="arcade-image">
        <canvas id="gameCanvas" width="400" height="400"></canvas>
    </div>
    <p>Score: <span id="score">0</span></p>
    <p>High Score: <span id="high-score"><?php echo $high_score ?? '0'; ?></span></p>
    <h1><a href="tetris.php" style="background-color:white;text-decoration:none;padding:5px;border:solid white;">Also Try Tetris!</a></h1>

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

        let directionChangedThisFrame = false;

        function changeDirection(event) {
            if (directionChangedThisFrame) return; // Only allow one direction change per frame

            const key = event.keyCode;
            const goingUp = direction === "UP";
            const goingDown = direction === "DOWN";
            const goingLeft = direction === "LEFT";
            const goingRight = direction === "RIGHT";

            // Prevent instant reversal
            if ((key === 37 || key === 65) && !goingRight) direction = "LEFT";  // Left (A)
            if ((key === 38 || key === 87) && !goingDown) direction = "UP";    // Up (W)
            if ((key === 39 || key === 68) && !goingLeft) direction = "RIGHT";  // Right (D)
            if ((key === 40 || key === 83) && !goingUp) direction = "DOWN";    // Down (S)

            directionChangedThisFrame = true; // Mark that the direction has been changed this frame
        }

        function drawGame() {
            directionChangedThisFrame = false; // Reset the flag at the start of each frame

            ctx.fillStyle = "gray";
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

            // Check for collisions after updating the snake's head position
            if (collision(newHead)) {
                clearInterval(gameInterval);
                alert("Game Over! Your score: " + score);
                saveScore(score); // Save score when game ends
                resetGame();
                return;
            }

            // Check if the snake eats the food
            if (newX === food.x && newY === food.y) {
                score += 10;
                document.getElementById("score").innerText = score;
                food = randomFood();
            } else {
                // Remove the tail segment if no food is eaten
                snake.pop();
            }

            // Add the new head to the snake
            snake.unshift(newHead);
        }
        
        function saveScore(score) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "save_score.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onreadystatechange = function () {
                if (xhr.readyState === 4 && xhr.status === 200) {
                    console.log("Score saved successfully!");
                }
            };
            xhr.send("score=" + score);
        }

        function collision(head) {
            // Proper boundary check
            if (head.x < 0 || head.y < 0 || head.x >= canvas.width || head.y >= canvas.height) {
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
