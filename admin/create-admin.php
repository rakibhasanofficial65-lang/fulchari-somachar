<?php

require_once "../config/database.php";

$name = "Administrator";
$username = "admin";
$password = "admin123";

$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

$stmt = $pdo->prepare("
    INSERT INTO users (name, username, password)
    VALUES (?, ?, ?)
");

$stmt->execute([
    $name,
    $username,
    $hashedPassword
]);

echo "Admin User Created Successfully!<br><br>";

echo "Username: admin<br>";
echo "Password: admin123<br><br>";

echo "IMPORTANT: Login করার পর এই file (create-admin.php) delete করে দিন.";

?>