#!/usr/bin/env php
<?php
/**
 * Script CLI — Créer le premier administrateur
 * Usage : php database/create-admin.php email@example.com motdepasse
 */

if (PHP_SAPI !== 'cli') {
    http_response_code(403);
    echo "Accès interdit.\n";
    exit(1);
}

define('ROOT_PATH', dirname(__DIR__));
require ROOT_PATH . '/config/config.php';

$email    = $argv[1] ?? null;
$password = $argv[2] ?? null;

if (!$email || !$password) {
    echo "Usage : php database/create-admin.php email@example.com motdepasse\n";
    exit(1);
}

if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    echo "Email invalide.\n";
    exit(1);
}

if (strlen($password) < 8) {
    echo "Le mot de passe doit faire au moins 8 caractères.\n";
    exit(1);
}

try {
    $dsn = sprintf('mysql:host=%s;dbname=%s;charset=%s', DB_HOST, DB_NAME, DB_CHARSET);
    $pdo = new PDO($dsn, DB_USER, DB_PASS, [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    ]);

    $hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

    $stmt = $pdo->prepare(
        "INSERT INTO users (email, password_hash, role) VALUES (?, ?, 'admin')
         ON DUPLICATE KEY UPDATE password_hash = ?"
    );
    $stmt->execute([$email, $hash, $hash]);

    echo "Administrateur créé ou mis à jour : $email\n";
} catch (\PDOException $e) {
    echo "Erreur de base de données : " . $e->getMessage() . "\n";
    exit(1);
}
