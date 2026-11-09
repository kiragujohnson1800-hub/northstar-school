<?php
declare(strict_types=1);
require_once __DIR__ . '/../config/database.php';

header('Cache-Control: no-store');
$action = (string) ($_GET['action'] ?? '');
if ($_SERVER['REQUEST_METHOD'] === 'GET' && $action === 'csrf') {
    json_response(['csrf_token' => csrf_token()]);
}

$currentUser = $_SESSION['user'] ?? null;
if (!is_array($currentUser) || ($currentUser['role'] ?? '') !== 'admin') {
    json_response(['error' => 'Administrator access is required.'], 403);
}

if ($_SERVER['REQUEST_METHOD'] === 'GET') {
    if ($action !== 'list') {
        json_response(['error' => 'Unknown action.'], 400);
    }
    try {
        $users = db()->query(
            'SELECT id, full_name, email, role, is_active, created_at FROM users ORDER BY created_at DESC, id DESC'
        )->fetchAll();
        json_response(['users' => $users]);
    } catch (PDOException $exception) {
        error_log('Admin user-list database error: ' . $exception->getMessage());
        json_response(['error' => 'Users could not be loaded. Please try again.'], 500);
    }
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    json_response(['error' => 'Method not allowed.'], 405);
}

$input = $_POST;
if (str_contains($_SERVER['CONTENT_TYPE'] ?? '', 'application/json')) {
    $decoded = json_decode((string) file_get_contents('php://input'), true);
    if (!is_array($decoded)) {
        json_response(['error' => 'Invalid request body.'], 400);
    }
    $input = $decoded;
}

if (!csrf_is_valid(isset($input['csrf_token']) ? (string) $input['csrf_token'] : null)) {
    json_response(['error' => 'Your session expired. Refresh the page and try again.'], 419);
}

$action = (string) ($input['action'] ?? 'add');
try {
    if ($action === 'delete') {
        $userId = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);
        if (!$userId || $userId === (int) $currentUser['id']) {
            json_response(['error' => 'Choose a valid account other than your own.'], 422);
        }

        $statement = db()->prepare('DELETE FROM users WHERE id = :id AND role <> "admin"');
        $statement->execute(['id' => $userId]);
        if ($statement->rowCount() !== 1) {
            json_response(['error' => 'That account does not exist or cannot be removed.'], 404);
        }
        json_response(['message' => 'User account removed.']);
    }

    if ($action !== 'add') {
        json_response(['error' => 'Unknown action.'], 400);
    }

    $fullName = trim((string) ($input['full_name'] ?? ''));
    $email = strtolower(trim((string) ($input['email'] ?? '')));
    $password = (string) ($input['password'] ?? '');
    $role = (string) ($input['role'] ?? '');
    $allowedRoles = ['student', 'instructor', 'parent'];

    if (mb_strlen($fullName) < 2 || mb_strlen($fullName) > 120
        || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190
        || strlen($password) < 10 || strlen($password) > 4096
        || !in_array($role, $allowedRoles, true)) {
        json_response(['error' => 'Check the name, email, role, and password. Passwords must be at least 10 characters.'], 422);
    }

    $statement = db()->prepare(
        'INSERT INTO users (full_name, email, password, role) VALUES (:full_name, :email, :password, :role)'
    );
    $statement->execute([
        'full_name' => $fullName,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
        'role' => $role,
    ]);
    json_response(['message' => 'User account created.']);
} catch (PDOException $exception) {
    if ($exception->getCode() === '23000') {
        json_response(['error' => 'An account already uses that email address.'], 409);
    }
    error_log('Admin user-management database error: ' . $exception->getMessage());
    json_response(['error' => 'The request could not be completed. Please try again.'], 500);
}
