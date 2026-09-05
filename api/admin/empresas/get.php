<?php
/**
 * Endpoint GET: Obtener detalles de una empresa por ID (Admin)
 */
require_once __DIR__ . '/../../../controllers/AdminEmpresaController.php';
AdminEmpresaController::get((int)($_GET['id'] ?? 0));
