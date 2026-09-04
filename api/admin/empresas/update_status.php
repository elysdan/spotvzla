<?php
/**
 * Endpoint POST: Actualizar estado de una empresa (Aprobar/Rechazar) - Solo Admin
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/helpers.php';
require_once __DIR__ . '/../../../includes/auth.php';

Auth::requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Método HTTP no permitido. Use POST.', null, 405);
}

$input = getRequestData();
$id     = (int)($input['id'] ?? 0);
$estado = clean($input['estado'] ?? '');

if ($id <= 0) {
    jsonResponse(false, 'ID de comercio inválido.', null, 400);
}

if (!in_array($estado, ['aprobado', 'pendiente', 'rechazado'], true)) {
    jsonResponse(false, 'Estado no válido. Use aprobado, pendiente o rechazado.', null, 400);
}

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("UPDATE empresas SET estado = :estado, updated_at = NOW() WHERE id = :id");
    $stmt->execute([':estado' => $estado, ':id' => $id]);

    if ($stmt->rowCount() === 0) {
        jsonResponse(false, 'No se encontró el comercio o no hubo cambios.', null, 404);
    }

    jsonResponse(true, "Estado de la empresa actualizado a '{$estado}'.", [
        'id'     => $id,
        'estado' => $estado
    ]);

} catch (Throwable $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
    }
    jsonResponse(false, 'Error al actualizar el estado de la empresa.', null, 500);
}
