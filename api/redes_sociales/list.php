<?php
/**
 * Endpoint GET: Listar redes sociales activas (Público)
 */
require_once __DIR__ . '/../../controllers/AdminRedSocialController.php';
AdminRedSocialController::list(true);
