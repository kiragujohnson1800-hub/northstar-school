<?php
declare(strict_types=1);

if (PHP_SAPI !== 'cli') {
    http_response_code(404);
    exit;
}

require_once __DIR__ . '/config/database.php';

$prompt = static function (string $label): string {
    fwrite(STDOUT, $label);
    $value = fgets(STDIN);
    return $value === false ? '' : trim($value);
};

$fullName = $prompt('Administrator full name: ');
$email = strtolower($prompt('Administrator email: '));
fwrite(STDOUT, 'Administrator password (at least 10 characters): ');
$password = fgets(STDIN);
$password = $password === false ? '' : rtrim($password, "\r\n");

if (strlen($fullName) < 2 || strlen($fullName) > 120
    || !filter_var($email, FILTER_VALIDATE_EMAIL) || strlen($email) > 190
    || strlen($password) < 10 || strlen($password) > 4096) {
    fwrite(STDERR, "Invalid name, email, or password. No account was created.\n");
    exit(1);
}

try {
    $statement = db()->prepare(
        'INSERT INTO users (full_name, email, password, role) VALUES (:full_name, :email, :password, "admin")'
    );
    $statement->execute([
        'full_name' => $fullName,
        'email' => $email,
        'password' => password_hash($password, PASSWORD_DEFAULT),
    ]);
    fwrite(STDOUT, "Administrator account created successfully.\n");
} catch (PDOException $exception) {
    if ($exception->getCode() === '23000') {
        fwrite(STDERR, "That email address already has an account. No administrator was created.\n");
        exit(1);
    }
    fwrite(STDERR, "Could not create the administrator: " . $exception->getMessage() . "\n");
    exit(1);
}
