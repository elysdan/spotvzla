<?php
/**
 * Controlador para Maestro de Redes Sociales (Admin)
 */

require_once __DIR__ . '/../models/RedSocial.php';
require_once __DIR__ . '/../includes/auth.php';
require_once __DIR__ . '/../includes/helpers.php';

class AdminRedSocialController
{
    public static function list(bool $public = false): void
    {
        if (!$public) {
            Auth::requireAdmin();
        }

        try {
            $onlyActive = $public || (isset($_GET['all']) && $_GET['all'] != '1');
            $redes = RedSocial::getAll(!$onlyActive ? false : true);

            jsonResponse(true, 'Catálogo de redes sociales obtenido.', [
                'redes' => $redes,
                'total' => count($redes)
            ]);
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            }
            jsonResponse(false, 'Error al consultar las redes sociales.', null, 500);
        }
    }

    public static function get(): void
    {
        Auth::requireAdmin();

        $id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);
        if (!$id) {
            jsonResponse(false, 'ID de red social inválido.', null, 400);
        }

        try {
            $red = RedSocial::getById($id);
            if (!$red) {
                jsonResponse(false, 'Red social no encontrada.', null, 404);
            }

            jsonResponse(true, 'Datos de la red social obtenidos.', [
                'red' => $red
            ]);
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            }
            jsonResponse(false, 'Error al consultar los datos de la red social.', null, 500);
        }
    }

    public static function create(): void
    {
        Auth::requireAdmin();

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
            jsonResponse(false, 'Método HTTP no permitido. Use POST.', null, 405);
        }

        $input   = getRequestData();
        $nombre  = clean($input['nombre'] ?? '');
        $icono   = clean($input['icono'] ?? '');
        $urlBase = clean($input['url_base'] ?? '');
        $color   = clean($input['color'] ?? '');
        $activo  = isset($input['activo']) ? (int)$input['activo'] : 1;
        $orden   = isset($input['orden']) ? (int)$input['orden'] : 0;

        if (empty($nombre)) {
            jsonResponse(false, 'El nombre de la red social es obligatorio.', null, 400);
        }
        if (empty($icono)) {
            jsonResponse(false, 'La clase de icono Font Awesome es obligatoria (ej: fa-brands fa-facebook).', null, 400);
        }

        try {
            $existente = RedSocial::findByName($nombre);
            if ($existente) {
                jsonResponse(false, 'Ya existe una red social registrada con el nombre "' . $nombre . '".', null, 409);
            }

            $nuevoId = RedSocial::create([
                'nombre'   => $nombre,
                'icono'    => $icono,
                'url_base' => $urlBase,
                'color'    => $color,
                'activo'   => $activo,
                'orden'    => $orden
            ]);

            jsonResponse(true, 'Red social agregada exitosamente al maestro.', [
                'id' => $nuevoId
            ], 201);
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            }
            jsonResponse(false, 'Error al registrar la red social.', null, 500);
        }
    }

    public static function update(): void
    {
        Auth::requireAdmin();

        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'PUT'])) {
            jsonResponse(false, 'Método HTTP no permitido. Use POST o PUT.', null, 405);
        }

        $input   = getRequestData();
        $id      = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);
        $nombre  = clean($input['nombre'] ?? '');
        $icono   = clean($input['icono'] ?? '');
        $urlBase = clean($input['url_base'] ?? '');
        $color   = clean($input['color'] ?? '');
        $activo  = isset($input['activo']) ? (int)$input['activo'] : 1;
        $orden   = isset($input['orden']) ? (int)$input['orden'] : 0;

        if (!$id) {
            jsonResponse(false, 'ID de red social inválido.', null, 400);
        }
        if (empty($nombre)) {
            jsonResponse(false, 'El nombre de la red social es obligatorio.', null, 400);
        }
        if (empty($icono)) {
            jsonResponse(false, 'La clase de icono Font Awesome es obligatoria.', null, 400);
        }

        try {
            $actual = RedSocial::getById($id);
            if (!$actual) {
                jsonResponse(false, 'Red social no encontrada para editar.', null, 404);
            }

            $existente = RedSocial::findByName($nombre);
            if ($existente && (int)$existente['id'] !== $id) {
                jsonResponse(false, 'Ya existe otra red social con el nombre "' . $nombre . '".', null, 409);
            }

            RedSocial::update($id, [
                'nombre'   => $nombre,
                'icono'    => $icono,
                'url_base' => $urlBase,
                'color'    => $color,
                'activo'   => $activo,
                'orden'    => $orden
            ]);

            jsonResponse(true, 'Red social actualizada exitosamente.');
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            }
            jsonResponse(false, 'Error al actualizar la red social.', null, 500);
        }
    }

    public static function delete(): void
    {
        Auth::requireAdmin();

        if (!in_array($_SERVER['REQUEST_METHOD'], ['POST', 'DELETE'])) {
            jsonResponse(false, 'Método HTTP no permitido. Use POST o DELETE.', null, 405);
        }

        $input = getRequestData();
        $id    = filter_var($input['id'] ?? null, FILTER_VALIDATE_INT);

        if (!$id) {
            jsonResponse(false, 'ID de red social inválido.', null, 400);
        }

        try {
            $actual = RedSocial::getById($id);
            if (!$actual) {
                jsonResponse(false, 'Red social no encontrada para eliminar.', null, 404);
            }

            RedSocial::delete($id);

            jsonResponse(true, 'Red social eliminada del maestro correctamente.');
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            }
            jsonResponse(false, 'Error al eliminar la red social.', null, 500);
        }
    }
}
