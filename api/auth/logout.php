<?php
/**
 * Endpoint POST: Cierre de Sesión
 */
require_once __DIR__ . '/../../controllers/AuthController.php';
AuthController::logout();
