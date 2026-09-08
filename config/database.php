<?php

$host = getenv("DB_HOST") ?: "gateway01.ap-southeast-1.prod.aws.tidbcloud.com";
$port = getenv("DB_PORT") ?: "4000";
$dbname = getenv("DB_NAME") ?: "fulchari_somachar";
$username = getenv("DB_USER") ?: "YOUR_TIDB_USERNAME";
$password = getenv("DB_PASSWORD") ?: "YOUR_TIDB_PASSWORD";

$caFile = __DIR__ . "/ca.pem";

try {
    $dsn = "mysql:host={$host};port={$port};dbname={$dbname};charset=utf8mb4";

    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,

        Pdo\Mysql::ATTR_SSL_CA => $caFile,
        Pdo\Mysql::ATTR_SSL_VERIFY_SERVER_CERT => true,
    ];

    $pdo = new PDO(
        $dsn,
        $username,
        $password,
        $options
    );

} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}
