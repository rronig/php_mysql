<?php
$host = 'localhost';
$user = 'root';
$pass = '';
$db = "testdb";

try{
    $pdo = new PDO("mysql:host=$host; dbname=$db", $user, $pass);

    //$sql = "CREATE TABLE users (id INT(6) NOT NULL AUTO_INCREMENT PRIMARY KEY, 
    //username VARCHAR(30) NOT NULL,
    //password VARCHAR(30) NOT NULL )";

    //$pdo -> exec($sql);

    //echo "Table created successfully";

}catch(Exception $e){
    //echo "Error creating table:  ". $e->getMessage();
}
?> 