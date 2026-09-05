<?php
/**
 * Endpoint GET: Listar catálogo completo de redes sociales (Admin)
 */
require_once __DIR__ . '/../../../controllers/AdminRedSocialController.php';
AdminRedSocialController::list(false);
