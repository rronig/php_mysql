<?php
session_start(); // Start the session
include_once("config.php");

// Check if the user is logged in
if (empty($_SESSION['username'])) {
    header("Location: login.php");
    exit;
}

// Fetch movies from the database
$sql = "SELECT * FROM movies";
$selectMovies = $conn->prepare($sql);
$selectMovies->execute();
$movies_data = $selectMovies->fetchAll(PDO::FETCH_ASSOC);
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Movie Dashboard</title>
        <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
        <link rel="icon" href="/docs/5.1/assets/img/favicons/favicon.ico">
    </head>
    <body>
        <header class="navbar navbar-dark sticky-top bg-dark flex-md-nowrap p-0 shadow">
            <a class="navbar-brand col-md-3 col-lg-2 me-0 px-3" href="#">
                <?php echo "Welcome to the Movie Dashboard, " . htmlspecialchars($_SESSION['username']); ?>
            </a>
            <button class="navbar-toggler position-absolute d-md-none collapsed" type="button">
                <span class="navbar-toggler-icon"></span>
            </button>
            <input type="text" placeholder="Search" aria-label="Search" class="form-control form-control-dark w-50">
            <div class="navbar-nav">
                <div class="nav-item text-nowrap">
                    <a href="logout.php" class="nav-link px-3">Sign Out</a>
                </div>
            </div>
        </header>
        <div class="container-fluid">
            <div class="row">
                <nav id="sidebarMenu" class="col-md-3 col-lg-2 d-md-block bg-light sidebar collapse">
                    <div class="position-sticky pt-3">
                        <ul class="nav flex-column">
                            <?php if ($_SESSION['isadmin'] === 'true') { ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="home.php">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link active" href="dashboard.php">Dashboard</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="list_movies.php">Movies</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="bookings.php">Bookings</a>
                                </li>
                            <?php } else { ?>
                                <li class="nav-item">
                                    <a class="nav-link" href="home.php">Home</a>
                                </li>
                                <li class="nav-item">
                                    <a class="nav-link" href="bookings.php">Bookings</a>
                                </li>
                            <?php } ?>
                        </ul>
                    </div>
                </nav>
                <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                    <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                        <h1 class="h2">Movies</h1>
                    </div>
                    <div class="table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>#</th>
                                    <th>Movie Name</th>
                                    <th>Description</th>
                                    <th>Quality</th>
                                    <th>Rating</th>
                                    <th>Image</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($movies_data as $index => $movie) { ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($index + 1); ?></td>
                                        <td><?php echo htmlspecialchars($movie['moviename']); ?></td>
                                        <td><?php echo htmlspecialchars($movie['moviedesc']); ?></td>
                                        <td><?php echo htmlspecialchars($movie['moviequality']); ?></td>
                                        <td><?php echo htmlspecialchars($movie['movierating']); ?></td>
                                        <td><img src="<?php echo htmlspecialchars($movie['movieimage']); ?>" alt="Movie Image" style="width: 50px; height: 50px; border-radius: 5px;"></td>
                                    </tr>
                                <?php } ?>
                            </tbody>
                        </table>
                    </div>
                </main>
            </div>
        </div>
        <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
        <script src="https://cdn.jsdelivr.net/npm/feather-icons@4.28.0/dist/feather.min.js"></script>
        <script>
            feather.replace();
        </script>
    </body>
</html>
