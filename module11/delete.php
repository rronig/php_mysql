<?php
include "db.php";
if(isset($_GET['id'])){
    $id= $_GET['id'];
    $sql="DELETE FROM users WHERE id=:id";
    $stmt=$pdo->prepare($sql);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $user=$stmt->fetch(PDO::FETCH_ASSOC);
    if($stmt->execute()){
        echo"User deleted successfully";
    }else{
        "Error deleting user";
    }
}
?>