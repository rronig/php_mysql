<?php
session_start();

// Redirect to login if the user is not logged in
if (empty($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Check if the role is set in the session
if (!isset($_SESSION['role'])) {
    $_SESSION['role'] = 'user'; // Default to 'user' if role is not set
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

// Fetch all users (only for admins)
$users = [];
if ($_SESSION['role'] === 'admin') {
    $sql = "SELECT id, name, username, email, role FROM users";
    $result = $conn->query($sql);
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $users[] = $row;
        }
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0-alpha3/dist/css/bootstrap.min.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Montserrat:ital,wght@0,100..900;1,100..900&family=Rubik+Dirt&family=Rubik+Spray+Paint&family=Sigmar&display=swap" rel="stylesheet">
    <style>
        body {
            text-align: center;
            background: linear-gradient(87deg, rgb(87, 143, 202), rgb(209, 248, 239));
            background-attachment: fixed; /* Ensures the gradient covers the entire page */
            color: white;
            font-family: "Rubik Dirt", serif;
            padding:20px;
            margin:0px;
        }
        .card {
            background-color: #fff;
            border-radius: 15px;
            padding: 20px;
            box-shadow: 0 4px 10px #91AC8F;
            border: 2px solid transparent;
            background-clip: padding-box;
            margin-bottom: 20px;
        }
        .card:hover {
            transform: translateY(-5px);
            transition: all 0.3s ease;
        }
        .btn-primary {
            background: linear-gradient(to right, #66785F, #B2C9AD);
            border: none;
            transition: all 0.3s ease;
        }
        .btn-primary:hover {
            background: linear-gradient(to right, #66785F, #B2C9AD);
            transform: scale(1.05);
        }
        a {
            color: #6a11cb;
            text-decoration: none;
            font-weight: bold;
            transition: color 0.3s ease;
        }
        a:hover {
            color: #3673b5;
        }
        h2 {
            color: #4B5945;
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
        .logout-button:hover {
            background: #5C8FC7;
        }
        
        .montserrat{
            font-family: "Montserrat", serif;
            font-optical-sizing: auto;
            font-weight: 900;
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

    <div class="container">
        <h1 class="text-center mb-4">Welcome, <?php echo $_SESSION['username']; ?>!</h1>
        <!-- Game Links -->
        <div class="card">
            <h2>Games</h2>
            <div class="d-flex justify-content-center gap-3">
                <a href="snake.php" class="montserrat game-btn snake" style="margin-top: 20px;">Play Snake</a>
                <a href="tetris.php" class="montserrat game-btn tetris" style="margin-top: 20px;">Play Tetris</a>
                <a href="tetris-unlimited.php" class="montserrat game-btn tetris" style="margin-top: 20px;">Play Tetris Unlimited</a>
                <a href="chess.php" class="montserrat game-btn chess" style="margin-top: 20px;">Play Chess</a>
            </div>
        </div>

        <style>
            .game-btn {
                width: 350px; /* Adjust width */
                height: 500px; /* Make buttons vertically longer */
                border-radius: 10px;
                color: white;
                font-size: 20px;
                font-weight: bold;
                text-align: center;
                text-decoration: none;
                display: flex;
                align-items: center;
                justify-content: center;
                background-size: cover;
                background-position: center;
                transition: transform 0.3s ease, opacity 0.3s ease;
            }
            .game-btn:hover {
                transform: scale(1.05);
                opacity: 0.9;
            }
            .snake {
                background-image: url('snake-bg.png'); /* Replace with actual image */
            }
            .tetris {
                background-image: url('tetris-bg.svg'); /* Replace with actual image */
            }
            .chess {
                background-image: url('chess-bg.png'); /* Replace with actual image */
            }
        </style>



        <!-- User Management (Admin Only) -->
        <?php if ($_SESSION['role'] === 'admin'): ?>
    <div class="card">
        <h2>User Management</h2>
        <table class="table table-striped">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Name</th>
                    <th>Username</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($users as $user): ?>
                    <tr>
                        <td><?php echo $user['id']; ?></td>
                        <td><?php echo $user['name']; ?></td>
                        <td><?php echo $user['username']; ?></td>
                        <td><?php echo $user['email']; ?></td>
                        <td><?php echo $user['role']; ?></td>
                        <td>
                            <a href="edit_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-warning">Edit</a>
                            <a href="delete_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure you want to delete this user?');">Delete</a>
                            <?php if ($user['role'] !== 'admin'): ?>
                                <a href="promote_user.php?id=<?php echo $user['id']; ?>" class="btn btn-sm btn-success" onclick="return confirm('Are you sure you want to promote this user to admin?');">Promote to Admin</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
    </div>
</body>
</html>