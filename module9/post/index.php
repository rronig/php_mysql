<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Form</title>
    </head>
    <body>
        <form action="add.php" method="POST">
            <label for="name">Name: </label>
            <input type="text" name="name" id="name" placeholder="Name" required><br>
            <label for="username">Username: </label>
            <input type="text" name="username" id="username" placeholder="Username" required><br>
            <label for="email">Email: </label><br>
            <input type="email" name="email" id="email" placeholder="Email" required><br><br>
            <button type="submit" name="submit">Add</button>
        </form>
    </body>
</html>