<?php
/**
 * Modelo Empresa
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/MetodoPago.php';
require_once __DIR__ . '/EmpresaFoto.php';

class Empresa
{
    public static function getPublicList(string $categoria = '', string $zona = '', string $metodo = ''): array
    {
        $pdo = Database::getConnection();

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
                e.redes_sociales,
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

        $empresaIds = array_column($rows, 'id');
        $fotosMap = EmpresaFoto::getByEmpresas($empresaIds);

        $comercios = [];
        foreach ($rows as $r) {
            $pays = !empty($r['pays_str']) ? explode(',', $r['pays_str']) : [];
            unset($r['pays_str']);

            if (!empty($metodo) && !in_array($metodo, $pays, true)) {
                continue;
            }

            $r['id']    = (int)$r['id'];
            $r['lat']   = $r['lat'] !== null ? (float)$r['lat'] : 10.4975;
            $r['lng']   = $r['lng'] !== null ? (float)$r['lng'] : -66.8542;
            $r['r']     = (float)$r['r'];
            $r['rv']    = (int)$r['rv'];
            $r['open']  = (int)$r['open'];
            $r['del']   = (int)$r['del'];
            $r['ver']   = (int)$r['ver'];
            $r['d']     = round(mt_rand(3, 35) / 10, 1);
            $r['pays']  = $pays;
            $r['redes'] = !empty($r['redes_sociales']) ? json_decode($r['redes_sociales'], true) : new stdClass();
            $r['fotos'] = $fotosMap[$r['id']] ?? [];
            unset($r['redes_sociales']);

            $comercios[] = $r;
        }

        return $comercios;
    }

    public static function getPublicStats(): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN estado = 'aprobado' THEN 1 ELSE 0 END) as aprobadas,
                SUM(CASE WHEN estado = 'aprobado' AND verificado = 1 THEN 1 ELSE 0 END) as verificadas
            FROM empresas
        ");
        $row = $stmt->fetch(PDO::FETCH_ASSOC);
        return [
            'total'       => (int)($row['total'] ?? 0),
            'aprobadas'   => (int)($row['aprobadas'] ?? 0),
            'verificadas' => (int)($row['verificadas'] ?? 0)
        ];
    }

    public static function getById(int $id): ?array
    {
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
            return null;
        }

        // Obtener métodos de pago
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
        $empresa['fotos']          = EmpresaFoto::getByEmpresaId($id);

        return $empresa;
    }

    public static function getAllAdmin(?string $estadoFiltro = null): array
    {
        $pdo = Database::getConnection();

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
                e.redes_sociales,
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

        $empresaIds = array_column($rows, 'id');
        $fotosMap = EmpresaFoto::getByEmpresas($empresaIds);

        return array_map(function($r) use ($fotosMap) {
            $r['id']             = (int)$r['id'];
            $r['usuario_id']     = (int)$r['usuario_id'];
            $r['categoria_id']   = (int)$r['categoria_id'];
            $r['latitud']        = $r['latitud'] !== null ? (float)$r['latitud'] : null;
            $r['longitud']       = $r['longitud'] !== null ? (float)$r['longitud'] : null;
            $r['calificacion']   = (float)$r['calificacion'];
            $r['total_resenas']  = (int)$r['total_resenas'];
            $r['delivery']       = (int)$r['delivery'];
            $r['abierto']        = (int)$r['abierto'];
            $r['verificado']     = (int)$r['verificado'];
            $r['metodos_pago']   = !empty($r['metodos_slugs']) ? explode(',', $r['metodos_slugs']) : [];
            $r['redes_sociales'] = !empty($r['redes_sociales']) ? json_decode($r['redes_sociales'], true) : new stdClass();
            $r['fotos']          = $fotosMap[$r['id']] ?? [];
            unset($r['metodos_slugs']);
            return $r;
        }, $rows);
    }

    public static function getAdminStats(): array
    {
        $pdo = Database::getConnection();
        $countsStmt = $pdo->query("
            SELECT 
                SUM(CASE WHEN estado = 'pendiente' THEN 1 ELSE 0 END) AS pendientes,
                SUM(CASE WHEN estado = 'aprobado' THEN 1 ELSE 0 END) AS aprobados,
                SUM(CASE WHEN estado = 'rechazado' THEN 1 ELSE 0 END) AS rechazados,
                COUNT(*) AS total
            FROM empresas
        ");
        $stats = $countsStmt->fetch();

        return [
            'pendientes' => (int)($stats['pendientes'] ?? 0),
            'aprobados'  => (int)($stats['aprobados'] ?? 0),
            'rechazados' => (int)($stats['rechazados'] ?? 0),
            'total'      => (int)($stats['total'] ?? 0)
        ];
    }

    public static function create(array $data): int
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $redesJson = self::formatRedesSociales($data['redes_sociales'] ?? null);

            $sql = "INSERT INTO empresas (
                        usuario_id, nombre, rif, categoria_id, descripcion,
                        telefono, correo_contacto, direccion, zona,
                        latitud, longitud, logo_url, redes_sociales, estado, verificado
                    ) VALUES (
                        :uid, :nombre, :rif, :cid, :desc,
                        :tel, :correo, :dir, :zona,
                        :lat, :lng, :logo, :redes, :estado, 1
                    )";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':uid'    => (int)$data['usuario_id'],
                ':nombre' => trim($data['nombre']),
                ':rif'    => !empty($data['rif']) ? trim($data['rif']) : null,
                ':cid'    => (int)$data['categoria_id'],
                ':desc'   => !empty($data['descripcion']) ? trim($data['descripcion']) : null,
                ':tel'    => !empty($data['telefono']) ? trim($data['telefono']) : null,
                ':correo' => !empty($data['correo_contacto']) ? trim($data['correo_contacto']) : null,
                ':dir'    => trim($data['direccion']),
                ':zona'   => trim($data['zona']),
                ':lat'    => !empty($data['latitud']) ? (float)$data['latitud'] : null,
                ':lng'    => !empty($data['longitud']) ? (float)$data['longitud'] : null,
                ':logo'   => !empty($data['logo_url']) ? trim($data['logo_url']) : null,
                ':redes'  => $redesJson,
                ':estado' => in_array($data['estado'] ?? '', ['pendiente', 'aprobado', 'rechazado']) ? $data['estado'] : 'aprobado'
            ]);

            $empresaId = (int)$pdo->lastInsertId();

            if (!empty($data['metodos_pago'])) {
                MetodoPago::syncEmpresaMetodos($empresaId, $data['metodos_pago'], $pdo);
            }

            if (!empty($data['fotos']) && is_array($data['fotos'])) {
                EmpresaFoto::sync($empresaId, $data['fotos'], $pdo);
            }

            $pdo->commit();
            return $empresaId;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = Database::getConnection();
        $pdo->beginTransaction();

        try {
            $redesJson = self::formatRedesSociales($data['redes_sociales'] ?? null);

            $sql = "UPDATE empresas SET 
                        usuario_id      = :uid,
                        nombre          = :nombre,
                        rif             = :rif,
                        categoria_id    = :cid,
                        descripcion     = :desc,
                        telefono        = :tel,
                        correo_contacto = :correo,
                        direccion       = :dir,
                        zona            = :zona,
                        latitud         = :lat,
                        longitud        = :lng,
                        logo_url        = :logo,
                        redes_sociales  = :redes,
                        estado          = :estado,
                        updated_at      = NOW()
                    WHERE id = :id";

            $stmt = $pdo->prepare($sql);
            $stmt->execute([
                ':uid'    => (int)$data['usuario_id'],
                ':nombre' => trim($data['nombre']),
                ':rif'    => !empty($data['rif']) ? trim($data['rif']) : null,
                ':cid'    => (int)$data['categoria_id'],
                ':desc'   => !empty($data['descripcion']) ? trim($data['descripcion']) : null,
                ':tel'    => !empty($data['telefono']) ? trim($data['telefono']) : null,
                ':correo' => !empty($data['correo_contacto']) ? trim($data['correo_contacto']) : null,
                ':dir'    => trim($data['direccion']),
                ':zona'   => trim($data['zona']),
                ':lat'    => !empty($data['latitud']) ? (float)$data['latitud'] : null,
                ':lng'    => !empty($data['longitud']) ? (float)$data['longitud'] : null,
                ':logo'   => !empty($data['logo_url']) ? trim($data['logo_url']) : null,
                ':redes'  => $redesJson,
                ':estado' => in_array($data['estado'] ?? '', ['pendiente', 'aprobado', 'rechazado']) ? $data['estado'] : 'aprobado',
                ':id'     => $id
            ]);

            if (isset($data['metodos_pago'])) {
                MetodoPago::syncEmpresaMetodos($id, (array)$data['metodos_pago'], $pdo);
            }

            if (isset($data['fotos']) && is_array($data['fotos'])) {
                EmpresaFoto::sync($id, (array)$data['fotos'], $pdo);
            }

            $pdo->commit();
            return true;
        } catch (Throwable $e) {
            if ($pdo->inTransaction()) {
                $pdo->rollBack();
            }
            throw $e;
        }
    }

    public static function updateStatus(int $id, string $estado): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("UPDATE empresas SET estado = :estado, updated_at = NOW() WHERE id = :id");
        return $stmt->execute([
            ':estado' => in_array($estado, ['pendiente', 'aprobado', 'rechazado']) ? $estado : 'pendiente',
            ':id'     => $id
        ]);
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        
        // Obtener logo si existe para eliminar archivo físico
        $check = $pdo->prepare("SELECT logo_url FROM empresas WHERE id = :id LIMIT 1");
        $check->execute([':id' => $id]);
        $emp = $check->fetch();

        $stmt = $pdo->prepare("DELETE FROM empresas WHERE id = :id");
        $deleted = $stmt->execute([':id' => $id]);

        if ($deleted && !empty($emp['logo_url'])) {
            $relativePath = ltrim($emp['logo_url'], '/');
            $fullPath = __DIR__ . '/../' . $relativePath;
            if (file_exists($fullPath) && is_file($fullPath)) {
                @unlink($fullPath);
            }
        }

        return $deleted;
    }

    private static function formatRedesSociales($input): ?string
    {
        if (is_array($input)) {
            $clean = [];
            foreach ($input as $k => $v) {
                if ($k === 'otras' && is_array($v)) {
                    $otrasClean = [];
                    foreach ($v as $item) {
                        if (is_array($item)) {
                            $nom = trim((string)($item['nombre'] ?? ''));
                            $val = trim((string)($item['valor'] ?? ''));
                            $ico = trim((string)($item['icono'] ?? ''));
                            if ($nom !== '' && $val !== '') {
                                $entry = [
                                    'nombre' => $nom,
                                    'valor'  => $val
                                ];
                                if ($ico !== '') {
                                    $entry['icono'] = $ico;
                                }
                                $otrasClean[] = $entry;
                            }

                        }
                    }
                    if (!empty($otrasClean)) {
                        $clean['otras'] = $otrasClean;
                    }
                } elseif (!is_array($v)) {
                    $val = trim((string)$v);
                    if ($val !== '') {
                        $clean[$k] = $val;
                    }
                }
            }
            return !empty($clean) ? json_encode($clean, JSON_UNESCAPED_UNICODE) : null;
        }
        if (is_string($input) && !empty(trim($input))) {
            return trim($input);
        }
        return null;
    }
}
