<?php
require_once 'db.php';
header('Content-Type: application/json');

try {
    $stmt = $pdo->query("SELECT id, full_name, phone, dob, gender, insurance, created_at FROM patients ORDER BY id DESC");
    $patients = $stmt->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($patients);
} catch (PDOException $e) {
    echo json_encode(["status" => "error", "message" => $e->getMessage()]);
}
?>