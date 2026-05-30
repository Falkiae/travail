#!/usr/bin/env php
<?php
define('ROOT_PATH', dirname(__DIR__));
define('CONFIG_PATH', ROOT_PATH . '/config');
require_once CONFIG_PATH . '/config.php';

if (PHP_SAPI !== 'cli') {
    die('Ce script ne peut être exécuté qu\'en ligne de commande.');
}

$email    = isset($argv[1]) ? trim($argv[1]) : '';
$password = isset($argv[2]) ? $argv[2] : '';

if ($email === '' || $password === '') {
    die("Usage: php create-admin.php email@example.com motdepasse\n");
}

try {
    $pdo  = new PDO(
        'mysql:host=' . DB_HOST . ';dbname=' . DB_NAME . ';charset=' . DB_CHARSET,
        DB_USER, DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );
    $hash = password_hash($password, PASSWORD_BCRYPT);
    $pdo->prepare("INSERT INTO kn_users (email, password_hash, role) VALUES (?, ?, 'admin') ON DUPLICATE KEY UPDATE password_hash = ?")
        ->execute([$email, $hash, $hash]);
    echo "Admin créé : $email\n";
} catch (Exception $e) {
    die("Erreur : " . $e->getMessage() . "\n");
}
