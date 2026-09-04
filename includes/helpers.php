<?php
/**
 * Funciones de ayuda generales: Respuestas JSON, validación y sanitización
 */

require_once __DIR__ . '/../config/config.php';

/**
 * Enviar respuesta en formato JSON estandarizado
 */
function jsonResponse(bool $success, string $message = '', mixed $data = null, int $statusCode = 200): void {
    if (!headers_sent()) {
        http_response_code($statusCode);
        header('Content-Type: application/json; charset=utf-8');
    }

    echo json_encode([
        'success' => $success,
        'message' => $message,
        'data'    => $data,
        'timestamp' => date('c')
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

/**
 * Obtener datos de la petición (soporta tanto application/json como multipart/x-www-form-urlencoded)
 */
function getRequestData(): array {
    $raw = file_get_contents('php://input');
    if (!empty($raw)) {
        $decoded = json_decode($raw, true);
        if (is_array($decoded)) {
            return $decoded;
        }
    }

    return array_merge($_GET, $_POST);
}

/**
 * Sanitizar cadena de texto
 */
function clean(string $val): string {
    return htmlspecialchars(trim($val), ENT_QUOTES, 'UTF-8');
}

/**
 * Validar formato de correo electrónico
 */
function isValidEmail(string $email): bool {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
