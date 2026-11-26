<?php
$configFile = dirname(__DIR__) . '/config.php';
if (!file_exists($configFile)) {
    http_response_code(503);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Server database configuration is missing.']);
    exit;
}
$config = require $configFile;
try {
    $pdo = new PDO(
        "mysql:host={$config['host']};dbname={$config['name']};charset=utf8mb4",
        $config['user'],
        $config['pass'],
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION, PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC]
    );
} catch (PDOException $e) {
    http_response_code(503);
    header('Content-Type: application/json');
    echo json_encode(['success' => false, 'message' => 'Database connection unavailable.']);
    exit;
}
