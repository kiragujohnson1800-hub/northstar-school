<?php
declare(strict_types=1);
require_once __DIR__ . '/config/database.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: register.html');
    exit;
}

if (!csrf_is_valid($_POST['csrf_token'] ?? null)) {
    header('Location: register.html?error=session');
    exit;
}

$fullName = trim((string) ($_POST['full_name'] ?? ''));
$email = strtolower(trim((string) ($_POST['email'] ?? '')));
$password = (string) ($_POST['password'] ?? '');
$confirmation = (string) ($_POST['confirm_password'] ?? '');

if (mb_strlen($fullName) < 2 || mb_strlen($fullName) > 120
    || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190
    || strlen($password) < 10 || strlen($password) > 4096
    || !hash_equals($password, $confirmation)) {
    header('Location: register.html?error=invalid');
    exit;
}

try {
    $statement = db()->prepare(
        'INSERT INTO users (full_name, email, password, role) VALUES (:full_name, :email, :password, "student")'
    );
    $statement->execute([
        'full_name' => $fullName,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
    ]);
} catch (PDOException $exception) {
    if ($exception->getCode() === '23000') {
        header('Location: register.html?error=duplicate');
        exit;
    }
    error_log('Student registration database error: ' . $exception->getMessage());
    header('Location: register.html?error=unavailable');
    exit;
}

header('Location: index.html?registered=1');
exit;
