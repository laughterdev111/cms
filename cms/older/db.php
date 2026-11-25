<?php
$host = "localhost";
$user = "root";
$pass = "";
$dbname = "rwebeya_clinic";

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die(json_encode(["status" => "error", "message" => "Database Connection Failed: " . $e->getMessage()]));
}
?>