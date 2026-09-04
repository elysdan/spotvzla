<?php
/**
 * Endpoint GET: Listar todas las empresas con filtros (Solo Admin)
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/helpers.php';
require_once __DIR__ . '/../../../includes/auth.php';

Auth::requireAdmin();

try {
    $pdo = Database::getConnection();

    $estadoFiltro = clean($_GET['estado'] ?? '');

    $sql = "
        SELECT 
            e.id,
            e.nombre,
            e.rif,
            e.categoria_id,
            c.nombre AS categoria_nombre,
            c.slug AS categoria_slug,
            c.icono AS categoria_icono,
            e.usuario_id,
            u.nombre AS dueno_nombre,
            u.email AS dueno_email,
            e.descripcion,
            e.telefono,
            e.correo_contacto,
            e.direccion,
            e.zona,
            e.latitud,
            e.longitud,
            e.logo_url,
            e.estado,
            e.calificacion,
            e.total_resenas,
            e.rango_precio,
            e.delivery,
            e.abierto,
            e.verificado,
            e.created_at,
            GROUP_CONCAT(mp.slug ORDER BY mp.orden SEPARATOR ',') AS metodos_slugs
        FROM empresas e
        INNER JOIN categorias c ON c.id = e.categoria_id
        INNER JOIN usuarios u ON u.id = e.usuario_id
        LEFT JOIN empresa_metodos_pago emp ON emp.empresa_id = e.id
        LEFT JOIN metodos_pago mp ON mp.id = emp.metodo_pago_id
    ";

    $params = [];
    if (!empty($estadoFiltro) && in_array($estadoFiltro, ['pendiente', 'aprobado', 'rechazado'], true)) {
        $sql .= " WHERE e.estado = :estado ";
        $params[':estado'] = $estadoFiltro;
    }

    $sql .= " GROUP BY e.id ORDER BY e.id DESC ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $empresas = array_map(function($r) {
        $r['id'] = (int)$r['id'];
        $r['usuario_id'] = (int)$r['usuario_id'];
        $r['categoria_id'] = (int)$r['categoria_id'];
        $r['latitud'] = $r['latitud'] !== null ? (float)$r['latitud'] : null;
        $r['longitud'] = $r['longitud'] !== null ? (float)$r['longitud'] : null;
        $r['calificacion'] = (float)$r['calificacion'];
        $r['total_resenas'] = (int)$r['total_resenas'];
        $r['delivery'] = (int)$r['delivery'];
        $r['abierto'] = (int)$r['abierto'];
        $r['verificado'] = (int)$r['verificado'];
        $r['metodos_pago'] = !empty($r['metodos_slugs']) ? explode(',', $r['metodos_slugs']) : [];
        unset($r['metodos_slugs']);
        return $r;
    }, $rows);

    // Contadores para resumen del panel
    $countsStmt = $pdo->query("
        SELECT 
            SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) AS pendientes,
            SUM(CASE WHEN estado = 'aprobado' THEN 1 ELSE 0 END) AS aprobados,
            SUM(CASE WHEN estado = 'rechazado' THEN 1 ELSE 0 END) AS rechazados,
            COUNT(*) AS total
        FROM empresas
    ");
    $stats = $countsStmt->fetch();

    jsonResponse(true, 'Listado de empresas obtenido.', [
        'empresas' => $empresas,
        'stats'    => [
            'pendientes' => (int)($stats['pendientes'] ?? 0),
            'aprobados'  => (int)($stats['aprobados'] ?? 0),
            'rechazados' => (int)($stats['rechazados'] ?? 0),
            'total'      => (int)($stats['total'] ?? 0)
        ]
    ]);

} catch (Throwable $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
    }
    jsonResponse(false, 'Error al consultar las empresas.', null, 500);
}
