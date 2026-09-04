<?php
/**
 * Gestión de autenticación, roles y sesiones
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/helpers.php';

class Auth {
    /**
     * Iniciar sesión de usuario en la variable $_SESSION
     */
    public static function login(array $user): void {
        if (session_status() === PHP_SESSION_NONE && !headers_sent()) {
            @session_start();
        }
        
        if (session_status() === PHP_SESSION_ACTIVE && !headers_sent()) {
            @session_regenerate_id(true);
        }

        $_SESSION['user_id'] = (int)$user['id'];
        $_SESSION['user_name'] = $user['nombre'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['rol'];
        $_SESSION['logged_in_at'] = time();
    }

    /**
     * Cerrar sesión
     */
    public static function logout(): void {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(
                session_name(),
                '',
                time() - 42000,
                $params["path"],
                $params["domain"],
                $params["secure"],
                $params["httponly"]
            );
        }

        session_destroy();
    }

    /**
     * Verificar si hay una sesión activa
     */
    public static function check(): bool {
        return !empty($_SESSION['user_id']);
    }

    /**
     * Obtener datos del usuario activo
     */
    public static function user(): ?array {
        if (!self::check()) {
            return null;
        }

        return [
            'id'    => $_SESSION['user_id'],
            'nombre'=> $_SESSION['user_name'],
            'email' => $_SESSION['user_email'],
            'rol'   => $_SESSION['user_role']
        ];
    }

    /**
     * Verificar si el usuario en sesión es administrador
     */
    public static function isAdmin(): bool {
        return self::check() && ($_SESSION['user_role'] === 'admin');
    }

    /**
     * Requerir autenticación obligatoria para un endpoint
     */
    public static function requireAuth(): void {
        if (!self::check()) {
            jsonResponse(false, 'Acceso no autorizado. Por favor inicia sesión.', null, 401);
        }
    }

    /**
     * Requerir rol de administrador obligatorio
     */
    public static function requireAdmin(): void {
        self::requireAuth();
        if (!self::isAdmin()) {
            jsonResponse(false, 'Acceso denegado. Se requieren permisos de administrador.', null, 403);
        }
    }
}
