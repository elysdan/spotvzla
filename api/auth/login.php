<?php
/**
 * Endpoint POST: Iniciar Sesión (Login)
 */

require_once __DIR__ . '/../../config/database.php';
require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Método HTTP no permitido. Use POST.', null, 405);
}

$input = getRequestData();
$email = clean($input['email'] ?? '');
$password = $input['password'] ?? '';

if (empty($email) || empty($password)) {
    jsonResponse(false, 'Debe ingresar el correo y la contraseña.', null, 400);
}

if (!isValidEmail($email)) {
    jsonResponse(false, 'El formato del correo electrónico no es válido.', null, 400);
}

try {
    $pdo = Database::getConnection();

    $stmt = $pdo->prepare("SELECT `id`, `nombre`, `email`, `password_hash`, `telefono`, `rol`, `estado` FROM `usuarios` WHERE `email` = :email LIMIT 1");
    $stmt->execute([':email' => $email]);
    $user = $stmt->fetch();

    if (!$user || !password_verify($password, $user['password_hash'])) {
        jsonResponse(false, 'Credenciales incorrectas. Verifique su correo y contraseña.', null, 401);
    }

    if ($user['estado'] !== 'activo') {
        jsonResponse(false, 'Esta cuenta se encuentra inactiva o bloqueada.', null, 403);
    }

    // Actualizar último login
    $upd = $pdo->prepare("UPDATE `usuarios` SET `ultimo_login` = NOW() WHERE `id` = :id");
    $upd->execute([':id' => $user['id']]);

    // Iniciar sesión
    Auth::login($user);

    jsonResponse(true, 'Inicio de sesión exitoso.', [
        'user' => [
            'id'     => (int)$user['id'],
            'nombre' => $user['nombre'],
            'email'  => $user['email'],
            'rol'    => $user['rol']
        ]
    ]);

} catch (Throwable $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
    }
    jsonResponse(false, 'Ocurrió un error al procesar el inicio de sesión.', null, 500);
}
