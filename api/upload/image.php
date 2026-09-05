<?php
/**
 * Endpoint POST: Subida de imágenes segura (Logos y fotos de comercios)
 */

require_once __DIR__ . '/../../config/config.php';
require_once __DIR__ . '/../../includes/helpers.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    jsonResponse(false, 'Método no permitido. Use POST.', null, 405);
}

$file = $_FILES['image'] ?? $_FILES['imagen'] ?? null;

if (!$file || !isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
    $errCode = $file['error'] ?? UPLOAD_ERR_NO_FILE;
    $msg = match ($errCode) {
        UPLOAD_ERR_INI_SIZE, UPLOAD_ERR_FORM_SIZE => 'El archivo supera el tamaño máximo permitido por el servidor.',
        UPLOAD_ERR_PARTIAL => 'La imagen se subió parcialmente.',
        UPLOAD_ERR_NO_FILE => 'No se seleccionó ninguna imagen.',
        default => 'Error al procesar la subida del archivo.'
    };
    jsonResponse(false, $msg, null, 400);
}
$maxBytes = 5 * 1024 * 1024; // 5 MB

if ($file['size'] > $maxBytes) {
    jsonResponse(false, 'La imagen supera el límite permitido de 5 MB.', null, 400);
}

// Validar tipo MIME real del archivo
$finfo = finfo_open(FILEINFO_MIME_TYPE);
$mime = finfo_file($finfo, $file['tmp_name']);
finfo_close($finfo);

$allowedMimes = [
    'image/jpeg' => 'jpg',
    'image/png'  => 'png',
    'image/webp' => 'webp'
];

if (!isset($allowedMimes[$mime])) {
    jsonResponse(false, 'Formato no permitido. Solo se aceptan imágenes JPG, PNG o WebP.', null, 400);
}

$ext = $allowedMimes[$mime];
$filename = 'comercio_' . bin2hex(random_bytes(8)) . '_' . time() . '.' . $ext;
$uploadDir = __DIR__ . '/../../uploads/logos/';

if (!is_dir($uploadDir)) {
    mkdir($uploadDir, 0755, true);
}

$targetPath = $uploadDir . $filename;

if (!move_uploaded_file($file['tmp_name'], $targetPath)) {
    jsonResponse(false, 'No se pudo guardar la imagen en el servidor.', null, 500);
}

$publicUrl = 'uploads/logos/' . $filename;

jsonResponse(true, 'Imagen subida correctamente.', [
    'url'      => $publicUrl,
    'filename' => $filename,
    'size'     => $file['size']
]);
