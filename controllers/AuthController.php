<?php
/**
 * Controlador de Autenticación
 */

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

class AuthController
{
    public static function login(): void
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(false, 'Método HTTP no permitido. Use POST.', null, 405);
        }

        $input = getRequestData();
        $email = clean($input['email'] ?? '');
        $password = $input['password'] ?? '';

        if (empty($email) || empty($password)) {
            jsonResponse(false, 'Por favor, ingrese correo y contraseña.', null, 400);
        }

        try {
            $user = Usuario::findByEmail($email);
            if (!$user || !password_verify($password, $user['password_hash'])) {
                jsonResponse(false, 'Credenciales incorrectas. Verifique su correo y contraseña.', null, 401);
            }

            if ($user['estado'] !== 'activo') {
                jsonResponse(false, 'Tu cuenta se encuentra inactiva o suspendida.', null, 403);
            }

            Auth::login($user);

            jsonResponse(true, 'Inicio de sesión exitoso.', [
                'user' => Auth::user()
            ]);
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            } else {
                jsonResponse(false, 'Error interno del servidor al procesar la solicitud.', null, 500);
            }
        }
    }

    public static function logout(): void
    {
        Auth::logout();
        jsonResponse(true, 'Sesión cerrada correctamente.');
    }

    public static function me(): void
    {
        $user = Auth::user();
        if ($user) {
            jsonResponse(true, 'Usuario autenticado.', [
                'authenticated' => true,
                'user'          => $user
            ]);
        } else {
            jsonResponse(true, 'No hay sesión activa.', [
                'authenticated' => false,
                'user'          => null
            ]);
        }
    }
}
