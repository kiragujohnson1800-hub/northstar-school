<?php
declare(strict_types=1);
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: index.html');
    exit;
}

if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
    header('Location: index.html?error=session');
    exit;
}

$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$password = (string) ($_POST['password'] ?? '');
if (!filter_var($email, FILTER_VALIDATE_EMAIL) || $password === '') {
    header('Location: index.html?error=invalid');
    exit;
}

try {
    $statement = db()->prepare(
        'SELECT id, full_name, email, password, role FROM users WHERE email = :email AND is_active = 1 LIMIT 1'
    );
    $statement->execute(['email' => $email]);
    $user = $statement->fetch();
} catch (PDOException $exception) {
    error_log('Login database error: ' . $exception->getMessage());
    header('Location: index.html?error=unavailable');
    exit;
}

if (!$user || !password_verify($password, (string) $user['password'])) {
    header('Location: index.html?error=invalid');
    exit;
}

$destination = dashboard_for_role((string) $user['role']);
if ($destination === null) {
    error_log('Login rejected: unsupported role for user ID ' . (int) $user['id']);
    header('Location: index.html?error=invalid');
    exit;
}

session_regenerate_id(true);
$_SESSION['user'] = [
    'id' => (int) $user['id'],
    'full_name' => (string) $user['full_name'],
    'email' => (string) $user['email'],
    'role' => (string) $user['role'],
];
$_SESSION['csrf_token'] = bin2hex(random_bytes(32));

header('Location: ' . $destination);
exit;
