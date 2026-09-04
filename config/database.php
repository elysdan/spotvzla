<?php
/**
 * Conexión a Base de Datos MySQL mediante PDO
 */

require_once __DIR__ . '/config.php';

class Database {
    private static ?PDO $instance = null;

    /**
     * Obtener instancia única de conexión PDO (Singleton)
     */
    public static function getConnection(?string $overrideDbName = null): PDO {
        $dbName = $overrideDbName !== null ? $overrideDbName : DB_NAME;

        if (self::$instance === null || $overrideDbName !== null) {
            $dsn = "mysql:host=" . DB_HOST . ";port=" . DB_PORT;
            if ($dbName !== '') {
                $dsn .= ";dbname=" . $dbName;
            }
            $dsn .= ";charset=utf8mb4";

            $options = [
                PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES   => false,
                PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4 COLLATE utf8mb4_unicode_ci"
            ];

            try {
                $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);
                if ($overrideDbName === null) {
                    self::$instance = $pdo;
                }
                return $pdo;
            } catch (PDOException $e) {
                if (defined('APP_DEBUG') && APP_DEBUG) {
                    throw new Exception("Error de conexión a la Base de Datos: " . $e->getMessage());
                } else {
                    error_log("DB Connection Error: " . $e->getMessage());
                    throw new Exception("Error de conexión al servidor de base de datos.");
                }
            }
        }

        return self::$instance;
    }
}
