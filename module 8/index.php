<?php

$host = 'localhost';
$user = 'root';
$pass = '';
$db="db";

try{
    $pdo= new PDO("mysql:host=$host; dbname=db", $user, $pass);
    $sql="CREATE DATABASE users (id INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY, usernam VARCHAR(30) NOT NULL, password VARCHAR(30) NOT NULL)";
    $pdo -> exec($sql);
    echo"Table is created!";
}catch(Exception $e){
    echo "Table not created, something went wrong". $e->getMessage();
}
?> 