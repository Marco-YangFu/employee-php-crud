<?php
$dsn = 'mysql:host=localhost;dbname=testdb;charset=utf8mb4';
$user = 'dev';
$pass = 'marco';

try {
    $pdo = new PDO($dsn, $user, $pass,[
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);
} catch (PDOException $e) {
    exit('DB Connection failed:' . $e->getMessage());
}