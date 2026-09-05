<?php
/**
 * Controlador de Administración de Usuarios
 */

require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

class AdminUsuarioController
{
    public static function list(): void
    {
        Auth::requireAdmin();

        try {
            $usuarios = Usuario::getAllWithCounts();
            $total = count($usuarios);

            jsonResponse(true, 'Listado de usuarios obtenido.', [
                'usuarios' => $usuarios,
                'total'    => $total
            ]);
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            }
            jsonResponse(false, 'Error al consultar los usuarios.', null, 500);
        }
    }

    public static function create(): void
    {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(false, 'Método HTTP no permitido. Use POST.', null, 405);
        }

        $input    = getRequestData();
        $nombre   = clean($input['nombre'] ?? '');
        $email    = clean($input['email'] ?? '');
        $telefono = clean($input['telefono'] ?? '');
        $rol      = clean($input['rol'] ?? 'empresa');
        $password = $input['password'] ?? '';

        if (empty($nombre)) {
            jsonResponse(false, 'El nombre es obligatorio.', null, 400);
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(false, 'Debe ingresar un correo electrónico válido.', null, 400);
        }
        if (empty($password) || strlen($password) < 6) {
            jsonResponse(false, 'La contraseña debe tener al menos 6 caracteres.', null, 400);
        }

        try {
            $existing = Usuario::findByEmail($email);
            if ($existing) {
                jsonResponse(false, 'Ya existe un usuario registrado con este correo.', null, 409);
            }

            $userId = Usuario::create([
                'nombre'   => $nombre,
                'email'    => $email,
                'telefono' => $telefono,
                'rol'      => $rol,
                'password' => $password,
                'estado'   => 'activo'
            ]);

            jsonResponse(true, 'Usuario creado exitosamente.', [
                'usuario_id' => $userId,
                'nombre'     => $nombre,
                'email'      => $email,
                'rol'        => $rol
            ], 201);
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            }
            jsonResponse(false, 'Ocurrió un error al registrar el usuario.', null, 500);
        }
    }

    public static function get(int $id): void
    {
        Auth::requireAdmin();

        if ($id <= 0) {
            jsonResponse(false, 'ID de usuario no válido.', null, 400);
        }

        try {
            $user = Usuario::findById($id);
            if (!$user) {
                jsonResponse(false, 'Usuario no encontrado.', null, 404);
            }

            $user['total_empresas'] = Usuario::countEmpresasByUser($id);

            jsonResponse(true, 'Detalles de usuario obtenidos.', [
                'usuario' => $user
            ]);
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            }
            jsonResponse(false, 'Error al consultar el usuario.', null, 500);
        }
    }

    public static function update(): void
    {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(false, 'Método HTTP no permitido. Use POST.', null, 405);
        }

        $input    = getRequestData();
        $id       = (int)($input['id'] ?? 0);
        $nombre   = clean($input['nombre'] ?? '');
        $email    = clean($input['email'] ?? '');
        $telefono = clean($input['telefono'] ?? '');
        $rol      = clean($input['rol'] ?? 'empresa');
        $estado   = clean($input['estado'] ?? 'activo');
        $password = $input['password'] ?? '';

        if ($id <= 0) {
            jsonResponse(false, 'ID de usuario no válido.', null, 400);
        }
        if (empty($nombre)) {
            jsonResponse(false, 'El nombre es obligatorio.', null, 400);
        }
        if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            jsonResponse(false, 'Debe ingresar un correo electrónico válido.', null, 400);
        }
        if (!empty($password) && strlen($password) < 6) {
            jsonResponse(false, 'La nueva contraseña debe tener al menos 6 caracteres.', null, 400);
        }

        try {
            $existing = Usuario::findByEmail($email);
            if ($existing && (int)$existing['id'] !== $id) {
                jsonResponse(false, 'Ya existe otro usuario registrado con este correo.', null, 409);
            }

            $user = Usuario::findById($id);
            if (!$user) {
                jsonResponse(false, 'Usuario no encontrado.', null, 404);
            }

            Usuario::update($id, [
                'nombre'   => $nombre,
                'email'    => $email,
                'telefono' => $telefono,
                'rol'      => $rol,
                'estado'   => $estado,
                'password' => $password
            ]);

            jsonResponse(true, 'Usuario actualizado exitosamente.', [
                'usuario' => Usuario::findById($id)
            ]);
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            }
            jsonResponse(false, 'Error al actualizar el usuario.', null, 500);
        }
    }

    public static function delete(): void
    {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(false, 'Método HTTP no permitido. Use POST.', null, 405);
        }

        $input = getRequestData();
        $id = (int)($input['id'] ?? 0);

        if ($id <= 0) {
            jsonResponse(false, 'ID de usuario no válido.', null, 400);
        }

        $currentUser = Auth::user();
        if ($currentUser && (int)$currentUser['id'] === $id) {
            jsonResponse(false, 'No puedes eliminar tu propia cuenta de administrador en sesión.', null, 400);
        }

        try {
            $user = Usuario::findById($id);
            if (!$user) {
                jsonResponse(false, 'Usuario no encontrado.', null, 404);
            }

            $empresasCount = Usuario::countEmpresasByUser($id);
            if ($empresasCount > 0) {
                jsonResponse(false, "No se puede eliminar el usuario porque tiene {$empresasCount} comercio(s) asignado(s). Primero reasigna sus comercios a otro usuario.", null, 400);
            }

            Usuario::delete($id);

            jsonResponse(true, 'Usuario eliminado exitosamente.');
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            }
            jsonResponse(false, 'Error al eliminar el usuario.', null, 500);
        }
    }
}
