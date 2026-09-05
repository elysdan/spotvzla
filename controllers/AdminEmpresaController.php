<?php
/**
 * Controlador de Administración de Empresas
 */

require_once __DIR__ . '/../models/Empresa.php';
require_once __DIR__ . '/../models/Usuario.php';
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

class AdminEmpresaController
{
    public static function list(): void
    {
        Auth::requireAdmin();

        try {
            $estadoFiltro = clean($_GET['estado'] ?? '');
            $empresas = Empresa::getAllAdmin($estadoFiltro);
            $stats = Empresa::getAdminStats();

            jsonResponse(true, 'Listado de empresas obtenido.', [
                'empresas' => $empresas,
                'stats'    => $stats
            ]);
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            }
            jsonResponse(false, 'Error al consultar las empresas.', null, 500);
        }
    }

    public static function get(int $id): void
    {
        Auth::requireAdmin();

        if ($id <= 0) {
            jsonResponse(false, 'ID de empresa no válido.', null, 400);
        }

        try {
            $empresa = Empresa::getById($id);
            if (!$empresa) {
                jsonResponse(false, 'Empresa no encontrada.', null, 404);
            }

            jsonResponse(true, 'Detalles de empresa obtenidos.', [
                'empresa' => $empresa
            ]);
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            }
            jsonResponse(false, 'Error al consultar la empresa.', null, 500);
        }
    }

    public static function create(): void
    {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(false, 'Método HTTP no permitido. Use POST.', null, 405);
        }

        $input = getRequestData();
        $usuarioId   = (int)($input['usuario_id'] ?? 0);
        $nombre      = clean($input['nombre'] ?? '');
        $categoriaId = (int)($input['categoria_id'] ?? 0);
        $direccion   = clean($input['direccion'] ?? '');
        $zona        = clean($input['zona'] ?? '');

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

        try {
            $checkUser = Usuario::findById($usuarioId);
            if (!$checkUser) {
                jsonResponse(false, 'El usuario asignado no existe.', null, 404);
            }

            $empresaId = Empresa::create($input);

            jsonResponse(true, 'Comercio registrado con éxito.', [
                'empresa_id' => $empresaId,
                'nombre'     => $nombre
            ], 201);
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            }
            jsonResponse(false, 'Ocurrió un error al registrar la empresa.', null, 500);
        }
    }

    public static function update(): void
    {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(false, 'Método HTTP no permitido. Use POST.', null, 405);
        }

        $input = getRequestData();
        $id          = (int)($input['id'] ?? 0);
        $usuarioId   = (int)($input['usuario_id'] ?? 0);
        $nombre      = clean($input['nombre'] ?? '');
        $categoriaId = (int)($input['categoria_id'] ?? 0);
        $direccion   = clean($input['direccion'] ?? '');
        $zona        = clean($input['zona'] ?? '');

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

        try {
            $currentEmp = Empresa::getById($id);
            if (!$currentEmp) {
                jsonResponse(false, 'La empresa no existe.', null, 404);
            }

            $checkUser = Usuario::findById($usuarioId);
            if (!$checkUser) {
                jsonResponse(false, 'El usuario asignado no existe.', null, 404);
            }

            Empresa::update($id, $input);

            jsonResponse(true, 'Comercio actualizado con éxito.');
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            }
            jsonResponse(false, 'Error al actualizar el comercio.', null, 500);
        }
    }

    public static function updateStatus(): void
    {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(false, 'Método HTTP no permitido. Use POST.', null, 405);
        }

        $input  = getRequestData();
        $id     = (int)($input['id'] ?? 0);
        $estado = clean($input['estado'] ?? '');

        if ($id <= 0 || !in_array($estado, ['pendiente', 'aprobado', 'rechazado'], true)) {
            jsonResponse(false, 'Datos inválidos para actualizar el estado.', null, 400);
        }

        try {
            $updated = Empresa::updateStatus($id, $estado);
            if ($updated) {
                jsonResponse(true, 'Estado actualizado correctamente.', [
                    'id'     => $id,
                    'estado' => $estado
                ]);
            } else {
                jsonResponse(false, 'Empresa no encontrada.', null, 404);
            }
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            }
            jsonResponse(false, 'Error al actualizar el estado.', null, 500);
        }
    }

    public static function delete(): void
    {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(false, 'Método HTTP no permitido. Use POST.', null, 405);
        }

        $input = getRequestData();
        $id    = (int)($input['id'] ?? 0);

        if ($id <= 0) {
            jsonResponse(false, 'ID de comercio no válido.', null, 400);
        }

        try {
            $deleted = Empresa::delete($id);
            if ($deleted) {
                jsonResponse(true, 'Comercio eliminado permanentemente.');
            } else {
                jsonResponse(false, 'Comercio no encontrado o ya eliminado.', null, 404);
            }
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            }
            jsonResponse(false, 'Error al eliminar el comercio.', null, 500);
        }
    }

    public static function deleteFoto(): void
    {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(false, 'Método HTTP no permitido. Use POST.', null, 405);
        }

        $input = getRequestData();
        $fotoId = (int)($input['foto_id'] ?? $input['id'] ?? 0);

        if ($fotoId <= 0) {
            jsonResponse(false, 'ID de foto no válido.', null, 400);
        }

        try {
            $deleted = EmpresaFoto::delete($fotoId);
            if ($deleted) {
                jsonResponse(true, 'Foto eliminada correctamente.');
            } else {
                jsonResponse(false, 'Foto no encontrada o ya eliminada.', null, 404);
            }
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            }
            jsonResponse(false, 'Error al eliminar la foto.', null, 500);
        }
    }
}
