<?php
declare(strict_types=1);

// Base de données
define('DB_HOST', 'localhost');
define('DB_NAME', 'keepnew');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Application
define('BASE_URL', 'http://localhost');
define('APP_ENV', 'development');
define('ADMIN_PATH', 'admin');

// Fichiers
define('UPLOAD_DIR', ROOT_PATH . '/storage/uploads/');
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB

// Session
define('SESSION_NAME', 'keepnew_session');
define('CSRF_TOKEN_LENGTH', 32);
