<?php
/**
 * Controlador Público de Empresas
 */

require_once __DIR__ . '/../models/Empresa.php';
require_once __DIR__ . '/../models/Categoria.php';
require_once __DIR__ . '/../includes/helpers.php';

class EmpresaController
{
    public static function listPublic(): void
    {
        try {
            $categoria = clean($_GET['categoria'] ?? '');
            $zona      = clean($_GET['zona'] ?? '');
            $metodo    = clean($_GET['pago'] ?? '');

            $comercios  = Empresa::getPublicList($categoria, $zona, $metodo);
            $categorias = Categoria::getActiveWithCounts(true);
            $stats      = Empresa::getPublicStats();

            jsonResponse(true, 'Comercios obtenidos.', [
                'comercios'  => $comercios,
                'total'      => count($comercios),
                'categorias' => $categorias,
                'stats'      => $stats
            ]);
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            }
            jsonResponse(false, 'Error al consultar comercios.', null, 500);
        }
    }

    public static function detailPublic(int $id): void
    {
        try {
            if ($id <= 0) {
                jsonResponse(false, 'ID inválido.', null, 400);
            }

            $empresa = Empresa::getById($id);
            if (!$empresa || $empresa['estado'] !== 'aprobado') {
                jsonResponse(false, 'Comercio no encontrado.', null, 404);
            }

            jsonResponse(true, 'Detalle de comercio obtenido.', [
                'comercio' => $empresa
            ]);
        } catch (Throwable $e) {
            if (defined('APP_DEBUG') && APP_DEBUG) {
                jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
            }
            jsonResponse(false, 'Error al consultar detalle.', null, 500);
        }
    }
}
