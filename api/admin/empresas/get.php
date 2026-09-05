<?php
/**
 * Endpoint GET: Obtener detalles completos de una empresa por ID (Solo Admin)
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/helpers.php';
require_once __DIR__ . '/../../../includes/auth.php';

Auth::requireAdmin();

$id = (int)($_GET['id'] ?? 0);

if ($id <= 0) {
    jsonResponse(false, 'ID de empresa no válido.', null, 400);
}

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("
        SELECT 
            e.id,
            e.usuario_id,
            e.nombre,
            e.rif,
            e.categoria_id,
            c.nombre AS categoria_nombre,
            c.slug AS categoria_slug,
            e.descripcion,
            e.telefono,
            e.correo_contacto,
            e.direccion,
            e.zona,
            e.latitud,
            e.longitud,
            e.logo_url,
            e.redes_sociales,
            e.estado,
            e.calificacion,
            e.rango_precio,
            e.delivery,
            e.abierto,
            e.verificado,
            u.nombre AS dueno_nombre,
            u.email AS dueno_email
        FROM empresas e
        INNER JOIN categorias c ON c.id = e.categoria_id
        INNER JOIN usuarios u ON u.id = e.usuario_id
        WHERE e.id = :id
        LIMIT 1
    ");
    $stmt->execute([':id' => $id]);
    $empresa = $stmt->fetch();

    if (!$empresa) {
        jsonResponse(false, 'Empresa no encontrada.', null, 404);
    }

    // Obtener métodos de pago asociados
    $stmtPays = $pdo->prepare("
        SELECT mp.id, mp.slug, mp.nombre 
        FROM empresa_metodos_pago emp
        INNER JOIN metodos_pago mp ON mp.id = emp.metodo_pago_id
        WHERE emp.empresa_id = :id
        ORDER BY mp.orden ASC
    ");
    $stmtPays->execute([':id' => $id]);
    $pays = $stmtPays->fetchAll();

    $empresa['id']             = (int)$empresa['id'];
    $empresa['usuario_id']     = (int)$empresa['usuario_id'];
    $empresa['categoria_id']   = (int)$empresa['categoria_id'];
    $empresa['latitud']        = $empresa['latitud'] !== null ? (float)$empresa['latitud'] : null;
    $empresa['longitud']       = $empresa['longitud'] !== null ? (float)$empresa['longitud'] : null;
    $empresa['metodos_pago']   = array_column($pays, 'slug');
    $empresa['redes_sociales'] = !empty($empresa['redes_sociales']) ? json_decode($empresa['redes_sociales'], true) : new stdClass();

    jsonResponse(true, 'Detalles de empresa obtenidos.', [
        'empresa' => $empresa
    ]);

} catch (Throwable $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
    }
    jsonResponse(false, 'Error al consultar la empresa.', null, 500);
}
