<?php
/**
 * Endpoint GET: Consultar usuario autenticado actual
 */

require_once __DIR__ . '/../../includes/helpers.php';
require_once __DIR__ . '/../../includes/auth.php';

if (Auth::check()) {
    jsonResponse(true, 'Sesión activa.', [
        'authenticated' => true,
        'user' => Auth::user()
    ]);
} else {
    jsonResponse(true, 'No hay sesión activa.', [
        'authenticated' => false,
        'user' => null
    ]);
}
