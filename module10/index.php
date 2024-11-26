<?php
require_once 'config.php';
try{
    $sql="SELECT id, username, email, password FROM users";
    $stmt=$pdo->query($sql);
    $users=$stmt->fetchAll(PDO::FETCH_ASSOC);
}catch(PDOException $e){
    echo"Error: ". $e->getMessage();
}
?>