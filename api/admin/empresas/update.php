<?php
/**
 * Endpoint POST: Actualizar todos los datos de una empresa (CRUD Update - Solo Admin)
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/helpers.php';
require_once __DIR__ . '/../../../includes/auth.php';

Auth::requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Método HTTP no permitido. Use POST.', null, 405);
}

$input = getRequestData();

$id          = (int)($input['id'] ?? 0);
$usuarioId   = (int)($input['usuario_id'] ?? 0);
$nombre      = clean($input['nombre'] ?? '');
$rif         = clean($input['rif'] ?? '');
$categoriaId = (int)($input['categoria_id'] ?? 0);
$descripcion = clean($input['descripcion'] ?? '');
$telefono    = clean($input['telefono'] ?? '');
$correo      = clean($input['correo_contacto'] ?? '');
$direccion   = clean($input['direccion'] ?? '');
$zona        = clean($input['zona'] ?? '');
$latitud     = !empty($input['latitud']) ? (float)$input['latitud'] : null;
$longitud    = !empty($input['longitud']) ? (float)$input['longitud'] : null;
$logoUrl     = clean($input['logo_url'] ?? '');
$estado      = clean($input['estado'] ?? 'aprobado');
$metodosPago = is_array($input['metodos_pago'] ?? null) ? $input['metodos_pago'] : [];

$redesInput  = $input['redes_sociales'] ?? null;
$redesJson   = null;
if (is_array($redesInput)) {
    $cleanRedes = [];
    foreach ($redesInput as $rk => $rv) {
        $cleanVal = clean((string)$rv);
        if ($cleanVal !== '') {
            $cleanRedes[$rk] = $cleanVal;
        }
    }
    $redesJson = !empty($cleanRedes) ? json_encode($cleanRedes, JSON_UNESCAPED_UNICODE) : null;
} elseif (is_string($redesInput) && !empty($redesInput)) {
    $redesJson = $redesInput;
}

if ($id <= 0) {
    jsonResponse(false, 'ID de empresa no válido.', null, 400);
}
if ($usuarioId <= 0) {
    jsonResponse(false, 'Debe especificar un usuario dueño válido.', null, 400);
}
if (empty($nombre)) {
    jsonResponse(false, 'El nombre del comercio es obligatorio.', null, 400);
}
if ($categoriaId <= 0) {
    jsonResponse(false, 'Debe seleccionar una categoría válida.', null, 400);
}
if (empty($direccion)) {
    jsonResponse(false, 'La dirección es obligatoria.', null, 400);
}
if (empty($zona)) {
    jsonResponse(false, 'La zona o municipio es obligatorio.', null, 400);
}
if (!in_array($estado, ['aprobado', 'pendiente', 'rechazado'], true)) {
    $estado = 'aprobado';
}

$pdo = Database::getConnection();

try {
    // 1. Verificar existencia de la empresa
    $checkEmp = $pdo->prepare("SELECT id, logo_url FROM empresas WHERE id = :id LIMIT 1");
    $checkEmp->execute([':id' => $id]);
    $currentEmp = $checkEmp->fetch();
    if (!$currentEmp) {
        jsonResponse(false, 'La empresa no existe.', null, 404);
    }

    // 2. Verificar existencia del usuario
    $checkUser = $pdo->prepare("SELECT id FROM usuarios WHERE id = :uid LIMIT 1");
    $checkUser->execute([':uid' => $usuarioId]);
    if (!$checkUser->fetch()) {
        jsonResponse(false, 'El usuario asignado no existe.', null, 404);
    }

    // 3. Verificar existencia de la categoría
    $checkCat = $pdo->prepare("SELECT id FROM categorias WHERE id = :cid LIMIT 1");
    $checkCat->execute([':cid' => $categoriaId]);
    if (!$checkCat->fetch()) {
        jsonResponse(false, 'La categoría seleccionada no existe.', null, 404);
    }

    // Conservar logo anterior si no se envía uno nuevo
    if (empty($logoUrl) && !empty($currentEmp['logo_url'])) {
        $logoUrl = $currentEmp['logo_url'];
    }

    $pdo->beginTransaction();

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
        ':uid'    => $usuarioId,
        ':nombre' => $nombre,
        ':rif'    => $rif ?: null,
        ':cid'    => $categoriaId,
        ':desc'   => $descripcion ?: null,
        ':tel'    => $telefono ?: null,
        ':correo' => $correo ?: null,
        ':dir'    => $direccion,
        ':zona'   => $zona,
        ':lat'    => $latitud,
        ':lng'    => $longitud,
        ':logo'   => $logoUrl ?: null,
        ':redes'  => $redesJson,
        ':estado' => $estado,
        ':id'     => $id
    ]);

    // Actualizar métodos de pago: eliminar los anteriores y registrar los nuevos
    $delPays = $pdo->prepare("DELETE FROM empresa_metodos_pago WHERE empresa_id = :id");
    $delPays->execute([':id' => $id]);

    if (!empty($metodosPago)) {
        $mStmt = $pdo->query("SELECT id, slug FROM metodos_pago");
        $slugToId = $mStmt->fetchAll(PDO::FETCH_KEY_PAIR);

        $insPay = $pdo->prepare("INSERT IGNORE INTO empresa_metodos_pago (empresa_id, metodo_pago_id) VALUES (:eid, :pid)");

        foreach ($metodosPago as $pago) {
            $metodoId = null;
            if (is_numeric($pago)) {
                $metodoId = (int)$pago;
            } elseif (isset($slugToId[$pago])) {
                $metodoId = (int)$slugToId[$pago];
            }

            if ($metodoId !== null) {
                $insPay->execute([':eid' => $id, ':pid' => $metodoId]);
            }
        }
    }

    $pdo->commit();

    jsonResponse(true, 'Empresa actualizada exitosamente.', [
        'id'       => $id,
        'nombre'   => $nombre,
        'logo_url' => $logoUrl
    ]);

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    if (defined('APP_DEBUG') && APP_DEBUG) {
        jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
    }
    jsonResponse(false, 'Ocurrió un error al actualizar la empresa.', null, 500);
}
