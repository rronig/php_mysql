<?php 
$server="localhost";
$user="root";
$password=""; //$password='';
$dbname="mms";
try{
    $conn = new PDO("mysql:host=$server;dbname=$dbname", $user, $password);
}catch(PDOException $e){
    echo"Error code: ". $e->getMessage();
}
?>