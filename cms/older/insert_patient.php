<?php
require_once 'db.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $fullName = $_POST['full_name'] ?? '';
    $phone = $_POST['phone'] ?? '';
    $dob = $_POST['dob'] ?? '';
    $gender = $_POST['gender'] ?? '';
    $insurance = $_POST['insurance'] ?? '';

    if (empty($fullName) || empty($phone)) {
        echo json_encode(["status" => "error", "message" => "Full name and phone are required."]);
        exit();
    }

    try {
        $stmt = $pdo->prepare("INSERT INTO patients (full_name, phone, dob, gender, insurance) VALUES (?, ?, ?, ?, ?)");
        $stmt->execute([$fullName, $phone, $dob, $gender, $insurance]);
        
        echo json_encode(["status" => "success", "message" => "Patient registered successfully."]);
    } catch (PDOException $e) {
        echo json_encode(["status" => "error", "message" => "Database error: " . $e->getMessage()]);
    }
}
?>