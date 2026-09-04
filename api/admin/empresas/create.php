<?php
/**
 * Endpoint POST: Crear nueva empresa/comercio y asociarla a un usuario (Solo Admin)
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/helpers.php';
require_once __DIR__ . '/../../../includes/auth.php';

Auth::requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Método HTTP no permitido. Use POST.', null, 405);
}

$input = getRequestData();

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
$estado      = clean($input['estado'] ?? 'aprobado');
$metodosPago = is_array($input['metodos_pago'] ?? null) ? $input['metodos_pago'] : [];

// Validaciones mínimas
if ($usuarioId <= 0) {
    jsonResponse(false, 'Debe seleccionar o especificar un usuario dueño válido.', null, 400);
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
    // 1. Verificar existencia del usuario
    $checkUser = $pdo->prepare("SELECT id FROM usuarios WHERE id = :uid LIMIT 1");
    $checkUser->execute([':uid' => $usuarioId]);
    if (!$checkUser->fetch()) {
        jsonResponse(false, 'El usuario asignado no existe.', null, 404);
    }

    // 2. Verificar existencia de la categoría
    $checkCat = $pdo->prepare("SELECT id FROM categorias WHERE id = :cid LIMIT 1");
    $checkCat->execute([':cid' => $categoriaId]);
    if (!$checkCat->fetch()) {
        jsonResponse(false, 'La categoría seleccionada no existe.', null, 404);
    }

    $pdo->beginTransaction();

    $sql = "INSERT INTO empresas (
                usuario_id, nombre, rif, categoria_id, descripcion,
                telefono, correo_contacto, direccion, zona,
                latitud, longitud, estado, verificado
            ) VALUES (
                :uid, :nombre, :rif, :cid, :desc,
                :tel, :correo, :dir, :zona,
                :lat, :lng, :estado, 1
            )";

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
        ':estado' => $estado
    ]);

    $empresaId = (int)$pdo->lastInsertId();

    // Insertar métodos de pago
    if (!empty($metodosPago)) {
        // Obtener mapa de métodos de pago válidos (slug -> id)
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
                $insPay->execute([':eid' => $empresaId, ':pid' => $metodoId]);
            }
        }
    }

    $pdo->commit();

    jsonResponse(true, 'Comercio registrado con éxito.', [
        'empresa_id' => $empresaId,
        'nombre'     => $nombre
    ], 201);

} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }
    if (defined('APP_DEBUG') && APP_DEBUG) {
        jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
    }
    jsonResponse(false, 'Ocurrió un error al registrar la empresa.', null, 500);
}
