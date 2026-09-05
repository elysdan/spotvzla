<?php
/**
 * Endpoint POST: Eliminar una empresa (CRUD Delete - Solo Admin)
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/helpers.php';
require_once __DIR__ . '/../../../includes/auth.php';

Auth::requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Método HTTP no permitido. Use POST.', null, 405);
}

$input = getRequestData();
$id = (int)($input['id'] ?? 0);

if ($id <= 0) {
    jsonResponse(false, 'ID de empresa no válido.', null, 400);
}

try {
    $pdo = Database::getConnection();

    // Obtener datos de la empresa para limpiar foto si aplica
    $check = $pdo->prepare("SELECT id, nombre, logo_url FROM empresas WHERE id = :id LIMIT 1");
    $check->execute([':id' => $id]);
    $empresa = $check->fetch();

    if (!$empresa) {
        jsonResponse(false, 'La empresa no existe o ya fue eliminada.', null, 404);
    }

    $stmt = $pdo->prepare("DELETE FROM empresas WHERE id = :id");
    $stmt->execute([':id' => $id]);

    // Opcional: eliminar archivo de imagen física si está en uploads/logos/
    if (!empty($empresa['logo_url']) && str_starts_with($empresa['logo_url'], 'uploads/logos/')) {
        $filePath = __DIR__ . '/../../../' . $empresa['logo_url'];
        if (file_exists($filePath)) {
            @unlink($filePath);
        }
    }

    jsonResponse(true, "Comercio '{$empresa['nombre']}' eliminado correctamente.", [
        'id' => $id
    ]);

} catch (Throwable $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
    }
    jsonResponse(false, 'Ocurrió un error al eliminar el comercio.', null, 500);
}
