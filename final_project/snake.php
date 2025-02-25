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
            margin: 20px auto;
            width: 800px;
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
    <h1 class="sigmar-regular">Snake Game</h1>
    <canvas id="gameCanvas" width="400" height="400"></canvas>
    <h1 class="rubik-dirt-regular">Score: <span id="score">0</span></h1>
    <h1 class="rubik-dirt-regular">High Score: <span id="high-score"><?php echo $high_score ?? '0'; ?></span></h1>

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
            directionChangedThisFrame = false;

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

            // Edge teleportation logic
            if (newX < 0) newX = canvas.width - box;    // Left edge to right
            if (newX >= canvas.width) newX = 0;         // Right edge to left
            if (newY < 0) newY = canvas.height - box;   // Top edge to bottom
            if (newY >= canvas.height) newY = 0;        // Bottom edge to top

            let newHead = {x: newX, y: newY};

            // Check self-collision (but no wall collision anymore)
            if (collision(newHead)) {
                clearInterval(gameInterval);
                alert("Game Over! Your score: " + score);
                saveScore(score);
                resetGame();
                return;
            }

            if (newX === food.x && newY === food.y) {
                score += 10;
                document.getElementById("score").innerText = score;
                food = randomFood();
            } else {
                snake.pop();
            }

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
