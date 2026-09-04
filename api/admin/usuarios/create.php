<?php
/**
 * Endpoint POST: Crear nuevo usuario (Solo Admin)
 */

require_once __DIR__ . '/../../../config/database.php';
require_once __DIR__ . '/../../../includes/helpers.php';
require_once __DIR__ . '/../../../includes/auth.php';

Auth::requireAdmin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Método HTTP no permitido. Use POST.', null, 405);
}

$input = getRequestData();

$nombre   = clean($input['nombre'] ?? '');
$email    = clean($input['email'] ?? '');
$password = $input['password'] ?? '';
$telefono = clean($input['telefono'] ?? '');
$rol      = clean($input['rol'] ?? 'usuario');
$estado   = clean($input['estado'] ?? 'activo');

// Validaciones
if (empty($nombre)) {
    jsonResponse(false, 'El nombre completo es obligatorio.', null, 400);
}

if (empty($email) || !isValidEmail($email)) {
    jsonResponse(false, 'Debe ingresar un correo electrónico válido.', null, 400);
}

if (strlen($password) < 6) {
    jsonResponse(false, 'La contraseña debe tener al menos 6 caracteres.', null, 400);
}

$allowedRoles = ['admin', 'empresa', 'usuario'];
if (!in_array($rol, $allowedRoles, true)) {
    jsonResponse(false, 'El rol especificado no es válido.', null, 400);
}

$allowedStates = ['activo', 'inactivo', 'bloqueado'];
if (!in_array($estado, $allowedStates, true)) {
    $estado = 'activo';
}

try {
    $pdo = Database::getConnection();

    // Verificar si el correo ya existe
    $check = $pdo->prepare("SELECT id FROM usuarios WHERE email = :email LIMIT 1");
    $check->execute([':email' => $email]);
    if ($check->fetch()) {
        jsonResponse(false, 'Ya existe un usuario registrado con este correo electrónico.', null, 409);
    }

    // Hashear contraseña
    $hash = password_hash($password, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nombre, email, password_hash, telefono, rol, estado) 
            VALUES (:nombre, :email, :hash, :telefono, :rol, :estado)";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nombre'   => $nombre,
        ':email'    => $email,
        ':hash'     => $hash,
        ':telefono' => $telefono ?: null,
        ':rol'      => $rol,
        ':estado'   => $estado
    ]);

    $newId = (int)$pdo->lastInsertId();

    jsonResponse(true, 'Usuario creado exitosamente.', [
        'usuario' => [
            'id'       => $newId,
            'nombre'   => $nombre,
            'email'    => $email,
            'telefono' => $telefono,
            'rol'      => $rol,
            'estado'   => $estado
        ]
    ], 201);

} catch (Throwable $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
    }
    jsonResponse(false, 'Ocurrió un error al crear el usuario.', null, 500);
}
