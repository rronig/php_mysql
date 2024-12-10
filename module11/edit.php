<?php
include "db.php";
if(isset($_GET['id'])){
    $id= $_GET['id'];
    $sql="SELECT * FROM users WHERE id=:id";
    $stmt=$pdo->prepare($sql);
    $stmt->bindParam(":id", $id, PDO::PARAM_INT);
    $stmt->execute();
    $user=$stmt->fetch(PDO::FETCH_ASSOC);
    if($user){
        ?>
        <!DOCTYPE html>
        <html lang="en">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Edit User</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    background-color: #f9f9f9;
                    margin: 20px;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                }
                h2 {
                    color: #333;
                    text-align: center;
                }
                form {
                    background: #fff;
                    padding: 20px;
                    border-radius: 8px;
                    box-shadow: 0 4px 8px rgba(0, 0, 0, 0);
                    width: 300px;
                }
                label {
                    font-weight: bold;
                    display: block;
                    margin-bottom: 8px;
                    color: rgb(193, 186, 161);
                }
                input[type="text"],
                input[type="email"] {
                    width: 100%;
                    padding: 10px;
                    margin-bottom: 15px;
                    border: 1px solid rgb(193, 186, 161);
                    border-radius: 4px;
                    box-sizing: border-box;
                }
                input[type="submit"] {
                    background-color: rgb(165, 157, 132);
                    color: white;
                    border: none;
                    padding: 10px 15px;
                    text-align: center;
                    border-radius: 4px;
                    cursor: pointer;
                    width: 100%;
                }
                input[type="submit"]:hover {
                    background-color: rgb(193, 186, 161);
                }
                .form-container {
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div class="form-container">
                <h2>Edit User</h2>
                <form action="edit.php" method="POST">
                    <input type="hidden" name="id" value="<?php echo $user['id']; ?>">
                    <label for="username">Username:</label>
                    <input type="text" id="username" name="username" value="<?php echo htmlspecialchars($user['username']); ?>" required>

                    <label for="email">Email:</label>
                    <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user['email']); ?>" required>

                    <input type="submit" value="Save">
                </form>
            </div>
        </body>
        </html>
        <?php 
    }else{
        echo"User not found";
    }
}
if($_SERVER['REQUEST_METHOD']==='POST'){
    $id=$_POST['id'];
    $username=$_POST["username"];
    $email=$_POST['email'];
    $sql="UPDATE users SET username = :username, email = :email WHERE id = :id";
    $stmt=$pdo->prepare($sql);
    $stmt->bindParam(':id', $id, PDO::PARAM_INT);
    $stmt->bindParam(':username', $username, PDO::PARAM_STR);
    $stmt->bindParam(':email', $email, PDO::PARAM_STR);
if($stmt->execute()){
    echo"User updated successfully";
}else{
    "Error updating user";
}
}
?>