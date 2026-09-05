<?php
/**
 * Endpoint GET: Listado público de categorías con conteos de comercios
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../models/Categoria.php';
require_once __DIR__ . '/../../includes/helpers.php';

try {
    $onlyWithComercios = !isset($_GET['all']) || $_GET['all'] != '1';
    $categorias = Categoria::getActiveWithCounts($onlyWithComercios);

    jsonResponse(true, 'Categorías obtenidas.', [
        'categorias' => $categorias,
        'total'      => count($categorias)
    ]);
} catch (Throwable $e) {
    if (defined('APP_DEBUG') && APP_DEBUG) {
        jsonResponse(false, 'Error en servidor: ' . $e->getMessage(), null, 500);
    }
    jsonResponse(false, 'Error al consultar categorías.', null, 500);
}
