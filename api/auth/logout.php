<?php
/**
 * Endpoint POST/GET: Cerrar Sesión (Logout)
 */

require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/auth.php';

Auth::logout();

jsonResponse(true, 'Sesión cerrada correctamente.');
