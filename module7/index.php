<?php

//Setting MySQL database parameters
$host = 'localhost';
$user = 'root';
$pass = '';

//Connection in datatbase using PDO
//Beinning of a try block
//try{
    //Attemt to create a neq PDO object and connecting to a MySQL database
    //The connection string is contructed using the variables $host, $user and $pass

//    $conn = new PDO("mysql:host=$host, $user, $pass");

    //If the connection is succesful
//    echo "Connected";
//} catch (Exception $e){
//    echo "Not Connected";
//}
try{
    $conn= new PDO("mysql:host=$host", $user, $pass);
    $sql="CREATE DATABASE testdb";
    $conn -> exec($sql);
    echo"Database is created!";
}catch(Exception $e){
    echo "Database not created, something went wrong";
}
?> 