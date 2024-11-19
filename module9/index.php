<?php
$username=$_GET['username'];
$password=$_GET['password'];
echo"<br>";
echo $username;
echo"<br>";
echo $password;
?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Our PHP Form</title>
    </head>
    <body>
        <form action="">
            <label for="username">Username: </label><br>
            <input type="text" name="username" id="username" placeholder="Username"><br>
            <label for="password">Password: </label><br>
            <input type="password" name="password" id="password" placeholder="Password"><br><br>
            <input type="submit" value="Submit">
        </form>
    </body>
</html>