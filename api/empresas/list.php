<?php
/**
 * Endpoint GET: Listado público de comercios aprobados para el directorio y mapa
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';

try {
    $pdo = Database::getConnection();

    $categoria = clean($_GET['categoria'] ?? '');
    $zona      = clean($_GET['zona'] ?? '');
    $metodo    = clean($_GET['pago'] ?? '');

    $sql = "
        SELECT 
            e.id,
            e.nombre AS n,
            c.slug AS cat,
            c.nombre AS cat_nombre,
            e.zona AS z,
            e.direccion AS dir,
            e.latitud AS lat,
            e.longitud AS lng,
            e.calificacion AS r,
            e.total_resenas AS rv,
            e.rango_precio AS p,
            e.abierto AS open,
            e.delivery AS del,
            e.verificado AS ver,
            e.descripcion AS `desc`,
            e.telefono AS tel,
            e.logo_url,
            GROUP_CONCAT(mp.slug ORDER BY mp.orden SEPARATOR ',') AS pays_str
        FROM empresas e
        INNER JOIN categorias c ON c.id = e.categoria_id
        LEFT JOIN empresa_metodos_pago emp ON emp.empresa_id = e.id
        LEFT JOIN metodos_pago mp ON mp.id = emp.metodo_pago_id
        WHERE e.estado = 'aprobado'
    ";

    $params = [];

    if (!empty($categoria) && $categoria !== 'all') {
        $sql .= " AND c.slug = :cat ";
        $params[':cat'] = $categoria;
    }

    if (!empty($zona) && $zona !== 'all' && $zona !== 'toda Caracas') {
        $sql .= " AND e.zona = :zona ";
        $params[':zona'] = $zona;
    }

    $sql .= " GROUP BY e.id ORDER BY e.id DESC ";

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $rows = $stmt->fetchAll();

    $comercios = [];
    foreach ($rows as $r) {
        $pays = !empty($r['pays_str']) ? explode(',', $r['pays_str']) : [];
        unset($r['pays_str']);

        // Filtrado por método de pago si se especifica
        if (!empty($metodo) && !in_array($metodo, $pays, true)) {
            continue;
        }

        $r['id']   = (int)$r['id'];
        $r['lat']  = $r['lat'] !== null ? (float)$r['lat'] : 10.4975;
        $r['lng']  = $r['lng'] !== null ? (float)$r['lng'] : -66.8542;
        $r['r']    = (float)$r['r'];
        $r['rv']   = (int)$r['rv'];
        $r['open'] = (int)$r['open'];
        $r['del']  = (int)$r['del'];
        $r['ver']  = (int)$r['ver'];
        $r['d']    = round(mt_rand(3, 35) / 10, 1); // distancia simulada si no hay geolocalización
        $r['pays'] = $pays;

        $comercios[] = $r;
    }

    jsonResponse(true, 'Comercios obtenidos.', [
        'comercios' => $comercios,
        'total'     => count($comercios)
    ]);

} catch (Throwable $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
    }
    jsonResponse(false, 'Error al consultar comercios.', null, 500);
}
