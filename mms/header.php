<?php
include_once('config.php');

$sql = "SELECT id, password FROM users";
$stmt = $conn->prepare($sql);
$stmt->execute();
$users = $stmt->fetchAll(PDO::FETCH_ASSOC);

foreach ($users as $user) {
    $hashedPassword = password_hash($user['password'], PASSWORD_DEFAULT);
    $updateSql = "UPDATE users SET password = :password WHERE id = :id";
    $updateStmt = $conn->prepare($updateSql);
    $updateStmt->execute([':password' => $hashedPassword, ':id' => $user['id']]);
}

echo "Passwords hashed successfully.";
?>