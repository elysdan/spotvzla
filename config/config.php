<?php
/**
 * Configuración general de Spot Venezuela
 * Compatible con XAMPP local y DonWeb cPanel/Ferozo
 */

// Zona horaria para Venezuela
date_default_timezone_set('America/Caracas');

// Detección automática de entorno (local vs DonWeb)
$rawHost = $_SERVER['HTTP_HOST'] ?? 'cli';
$host = explode(':', $rawHost)[0];
$isLocal = in_array($host, ['localhost', '127.0.0.1', '::1', 'cli']) || PHP_SAPI === 'cli';

if ($isLocal) {
    define('DB_HOST', 'localhost');
    define('DB_PORT', 3306);
    define('DB_NAME', 'spotvzla');
    define('DB_USER', 'root');
    define('DB_PASS', ''); // Contraseña por defecto en XAMPP
    define('APP_ENV', 'development');
    define('APP_DEBUG', true);
} else {
    // Configuración para DonWeb (Reemplazar con tus credenciales creadas en MySQL DonWeb)
    define('DB_HOST', 'localhost');
    define('DB_PORT', 3306);
    define('DB_NAME', 'cXXXX_spotvzla');   // Prefijo de DonWeb
    define('DB_USER', 'cXXXX_spotuser');
    define('DB_PASS', 'TuPasswordSeguro');
    define('APP_ENV', 'production');
    define('APP_DEBUG', false);
}

// Configuración de Sesión segura
if (session_status() === PHP_SESSION_NONE && PHP_SAPI !== 'cli') {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_samesite', 'Lax');
    if (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off') {
        ini_set('session.cookie_secure', 1);
    }
    session_name('SPOTVZLA_SESSION');
    session_start();
}
