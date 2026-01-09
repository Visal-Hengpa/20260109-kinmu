<?php
$host = 'localhost';
$dbname = 'kintaidb';
$username = 'kintaiuser';
$password = 'kintaipass123';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_EMULATE_PREPARES, false);
} catch (PDOException $e) {
    die("データベース接続に失敗しました: " . $e->getMessage());
}
?>