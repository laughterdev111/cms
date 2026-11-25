<?php
declare(strict_types=1);

define('BASE_PATH', dirname(__DIR__));
if (session_status() !== PHP_SESSION_ACTIVE) {
    $secure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    session_set_cookie_params(['httponly'=>true, 'secure'=>$secure, 'samesite'=>'Lax']);
    session_start();
}

function load_env(string $path): void {
    if (!is_file($path)) return;
    foreach (file($path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $line) {
        $line = trim($line);
        if ($line === '' || str_starts_with($line, '#') || !str_contains($line, '=')) continue;
        [$key, $value] = explode('=', $line, 2);
        $value = trim($value);
        if (strlen($value) >= 2 && (($value[0] === '"' && substr($value,-1) === '"') || ($value[0] === "'" && substr($value,-1) === "'"))) {
            $value = substr($value, 1, -1);
        }
        $_ENV[trim($key)] = $value;
    }
}
load_env(BASE_PATH . '/.env');

function envv(string $key, string $default = ''): string {
    return (string)($_ENV[$key] ?? $default);
}
function db(): PDO {
    static $pdo = null;
    if ($pdo instanceof PDO) return $pdo;
    $dsn = 'mysql:host='.envv('DB_HOST','127.0.0.1').';port='.envv('DB_PORT','3306').';dbname='.envv('DB_NAME','rwebeya_clinic').';charset=utf8mb4';
    $pdo = new PDO($dsn, envv('DB_USER','root'), envv('DB_PASS',''), [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false
    ]);
    return $pdo;
}
function e(?string $value): string { return htmlspecialchars((string)$value, ENT_QUOTES, 'UTF-8'); }
function redirect(string $path): never {
    header('Location: '.rtrim(envv('APP_URL',''), '/').'/'.ltrim($path,'/'));
    exit;
}
function csrf_token(): string {
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(32));
    return $_SESSION['csrf'];
}
function verify_csrf(): void {
    $token = $_POST['csrf'] ?? '';
    if (!is_string($token) || !hash_equals(csrf_token(), $token)) {
        http_response_code(419); exit('Invalid form token. Refresh the page and try again.');
    }
}
function flash(string $type, string $message): void { $_SESSION['flash'] = ['type'=>$type,'message'=>$message]; }
function take_flash(): ?array {
    $f = $_SESSION['flash'] ?? null; unset($_SESSION['flash']); return $f;
}
function current_user(): ?array { return $_SESSION['user'] ?? null; }
function require_login(): void {
    if (!current_user()) redirect('login.php');
}
function require_role(array $roles): void {
    require_login();
    if (!in_array(current_user()['role'], $roles, true)) {
        http_response_code(403); exit('403 - You do not have permission to access this page.');
    }
}
function audit(string $action, ?string $type = null, ?int $id = null): void {
    $u = current_user();
    $stmt = db()->prepare('INSERT INTO audit_log(user_id, action, entity_type, entity_id) VALUES(?,?,?,?)');
    $stmt->execute([$u['id'] ?? null, $action, $type, $id]);
}
function app_url(string $path = ''): string { return rtrim(envv('APP_URL',''), '/').'/'.ltrim($path,'/'); }
