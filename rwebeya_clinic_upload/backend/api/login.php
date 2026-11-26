<?php
session_start();
header('Content-Type: application/json');
require __DIR__ . '/db.php';
$data = json_decode(file_get_contents('php://input'), true) ?? [];
$email = filter_var(trim($data['email'] ?? ''), FILTER_VALIDATE_EMAIL);
$password = $data['password'] ?? '';
if (!$email || $password === '') {
    http_response_code(422);
    echo json_encode(['success' => false, 'message' => 'Enter a valid email and password.']);
    exit;
}
$stmt = $pdo->prepare('SELECT id, full_name, email, password_hash, role, email_verified FROM users WHERE email = ? LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();
if (!$user || !$user['email_verified'] || !password_verify($password, $user['password_hash'])) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Invalid login or email not verified.']);
    exit;
}
session_regenerate_id(true);
$_SESSION['user_id'] = $user['id'];
$_SESSION['role'] = $user['role'];
echo json_encode(['success' => true, 'role' => $user['role'], 'name' => $user['full_name']]);
