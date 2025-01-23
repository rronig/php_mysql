<?php
include_once("config.php");
$id=$_GET['id'];
$sql="DELETE FROM users WHERE id=:id";
$deleteuser=$conn->prepare($sql);
$deleteuser->bindParam(":id", $id);
$deleteuser->execute();
header("Location: dashboard.php");
?>