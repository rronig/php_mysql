<?php

$host = 'localhost';
$dbname = 'user_managment';
$username = "root";
$password = ""; //$password = "";

try{
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);

    //Set PDO error mode to exception
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // echo "Connected";

    if($_SERVER["REQUEST_METHOD"] == "POST"){
        //Get form data
        $user = $_POST['username'];
        $email = $_POST['email'];
        $pass = $_POST['password'];

        if(empty($user) || empty($email) || empty($pass)){
            echo "All the fields are required!!";
            exit;
        }

        // Hash the password
        $hashed_password = password_hash($pass, PASSWORD_BCRYPT);

        // Preapare the SQL statement
        $sql = "INSERT INTO users (username, email, password) VALUES (:username, :email, :Password)";
        $stmt = $pdo->prepare($sql);

        //Bind parameters
        $stmt-> bindParam(':username', $user, PDO::PARAM_STR);
        $stmt-> bindParam(':email', $email, PDO::PARAM_STR);
        $stmt-> bindParam(':Password', $hashed_password, PDO::PARAM_STR);

        //Execute the statement
        if($stmt->execute()){
            echo "Signup succesful! You can now log in!!";
        }else{
            echo "Something went wrong. Please try again";
        }


    }

}catch(PDOException $e){
    echo "Error: ".$e->getMessage();
}
?> 