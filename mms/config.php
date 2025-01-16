<?php 
$server="localhost";
$user="root";
$password=""; //$password='';
$dbname="mms";
try{
    $conn = new PDO("mysql:host=$server;dbname=$dbname", $user, $password);
    echo"<h1>Success</h1>";
}catch(PDOException $e){
    echo"Error code: ". $e->getMessage();
}
?>