<?php
try{
    $pdo = new PDO("mysql:host=localhost;dbname=testdb", "root", "");
    $sql="ALTER TABLE users DROP COLUMN email";
    $pdo->exec($sql);
    echo"Column dropped successfully";
}catch(PDOException $e){
    echo $e->getMessage();
}
?>