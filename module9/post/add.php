<?php
//Here we include database connection
include_once("config.php");

//isset() determines if variable is active and not null
if(isset($_POST['submit'])){
    $name=$_POST['name'];
    $username=$_POST['username'];
    $email=$_POST['email'];
    $sql="INSERT INTO users(name, username, email) VALUES(:name, :username, :email)";
    $sqlQuery=$connect->prepare($sql);
    $sqlQuery->bindParam(':name', $name);
    $sqlQuery->bindParam(':username', $username);
    $sqlQuery->bindParam(':email', $email);
    $sqlQuery->execute();
    echo"Added User";
    
    
}
?>