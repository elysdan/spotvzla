<?php
/**
 * Endpoint GET: Obtener detalles de un usuario por ID
 */

require_once __DIR__ . '/../../../controllers/AdminUsuarioController.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
AdminUsuarioController::get($id);
