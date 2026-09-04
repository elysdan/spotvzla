<?php
/**
 * Endpoint GET: Listar usuarios (Solo Admin)
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/helpers.php';
require_once __DIR__ . '/../../../includes/auth.php';

Auth::requireAdmin();

try {
    $pdo = Database::getConnection();

    $sql = "
        SELECT 
            u.id,
            u.nombre,
            u.email,
            u.telefono,
            u.rol,
            u.estado,
            u.ultimo_login,
            u.created_at,
            COUNT(e.id) AS total_empresas
        FROM usuarios u
        LEFT JOIN empresas e ON e.usuario_id = u.id
        GROUP BY u.id
        ORDER BY u.id DESC
    ";

    $stmt = $pdo->query($sql);
    $users = $stmt->fetchAll();

    jsonResponse(true, 'Listado de usuarios obtenido.', [
        'usuarios' => $users,
        'total'    => count($users)
    ]);

} catch (Throwable $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
    }
    jsonResponse(false, 'Error al consultar los usuarios.', null, 500);
}
