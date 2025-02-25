<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Space Shooter</title>
    <style>
        body {
            margin: 0;
            overflow: hidden;
        }
        #gameCanvas {
            background-color: #000;
            display: block;
            margin: 0 auto;
        }
    </style>
</head>
<body>
    <canvas id="gameCanvas"></canvas>
    <script>
        const canvas = document.getElementById('gameCanvas');
        const ctx = canvas.getContext('2d');
        canvas.width = 400;
        canvas.height = 600;

        let player = { x: canvas.width / 2, y: canvas.height - 40, width: 40, height: 40, speed: 5 };
        let bullets = [];
        let enemies = [];
        let score = 0;

        function Bullet(x, y) {
            this.x = x;
            this.y = y;
            this.width = 5;
            this.height = 10;
            this.speed = 5;
        }

        Bullet.prototype.update = function() {
            this.y -= this.speed;
        };

        Bullet.prototype.draw = function() {
            ctx.fillStyle = 'white';
            ctx.fillRect(this.x, this.y, this.width, this.height);
        };

        function Enemy() {
            this.x = Math.random() * (canvas.width - 40);
            this.y = Math.random() * -100;
            this.width = 40;
            this.height = 40;
            this.speed = 3;
        }

        Enemy.prototype.update = function() {
            this.y += this.speed;
        };

        Enemy.prototype.draw = function() {
            ctx.fillStyle = 'red';
            ctx.fillRect(this.x, this.y, this.width, this.height);
        };

        function drawPlayer() {
            ctx.fillStyle = 'green';
            ctx.fillRect(player.x, player.y, player.width, player.height);
        }

        function drawScore() {
            ctx.font = '20px Arial';
            ctx.fillStyle = 'white';
            ctx.fillText('Score: ' + score, 10, 30);
        }

        function gameLoop() {
            ctx.clearRect(0, 0, canvas.width, canvas.height);

            // Update bullets
            for (let i = 0; i < bullets.length; i++) {
                bullets[i].update();
                if (bullets[i].y < 0) bullets.splice(i, 1);
                bullets[i].draw();
            }

            // Update enemies
            for (let i = 0; i < enemies.length; i++) {
                enemies[i].update();
                if (enemies[i].y > canvas.height) {
                    enemies.splice(i, 1);
                }
                enemies[i].draw();
            }

            // Draw player and score
            drawPlayer();
            drawScore();

            requestAnimationFrame(gameLoop);
        }

        function shootBullet() {
            bullets.push(new Bullet(player.x + player.width / 2 - 2, player.y));
        }

        function spawnEnemy() {
            if (Math.random() < 0.02) {
                enemies.push(new Enemy());
            }
        }

        window.addEventListener('keydown', function(e) {
            if (e.key === 'ArrowLeft' && player.x > 0) player.x -= player.speed;
            if (e.key === 'ArrowRight' && player.x < canvas.width - player.width) player.x += player.speed;
            if (e.key === ' ') shootBullet();
        });

        function checkCollisions() {
            for (let i = 0; i < bullets.length; i++) {
                for (let j = 0; j < enemies.length; j++) {
                    if (bullets[i].x < enemies[j].x + enemies[j].width &&
                        bullets[i].x + bullets[i].width > enemies[j].x &&
                        bullets[i].y < enemies[j].y + enemies[j].height &&
                        bullets[i].y + bullets[i].height > enemies[j].y) {
                            bullets.splice(i, 1);
                            enemies.splice(j, 1);
                            score++;
                        }
                }
            }
        }

        function game() {
            gameLoop();
            spawnEnemy();
            checkCollisions();
        }

        setInterval(game, 1000000 / 60); // 60 FPS
    </script>
</body>
</html>
