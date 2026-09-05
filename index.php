<?php
/**
 * Spot - Punto de entrada principal (Arquitectura MVC Modular)
 */

require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/database.php';
require_once __DIR__ . '/models/Categoria.php';
require_once __DIR__ . '/models/Empresa.php';

try {
    $statsPublicas     = Empresa::getPublicStats();
    $categoriasActivas = Categoria::getActiveWithCounts(true);
    $comerciosAprobados = Empresa::getPublicList();
} catch (Throwable $e) {
    $statsPublicas     = ['total' => 0, 'aprobadas' => 0, 'verificadas' => 0];
    $categoriasActivas = [];
    $comerciosAprobados = [];
}
?>
<!DOCTYPE html>
<html lang="es" data-theme="dark">

<head>
  <?php include __DIR__ . '/views/layouts/head.php'; ?>
</head>

<body>
  <!-- Sprite de Iconos SVG -->
  <?php include __DIR__ . '/views/layouts/svg_icons.php'; ?>

  <!-- Cabecera y Navegación Móvil -->
  <?php include __DIR__ . '/views/layouts/header.php'; ?>

  <!-- ============ SECCIONES / VISTAS PRINCIPALES ============ -->
  <?php include __DIR__ . '/views/sections/inicio.php'; ?>
  <?php include __DIR__ . '/views/sections/mapa.php'; ?>
  <?php include __DIR__ . '/views/sections/categorias.php'; ?>
  <?php include __DIR__ . '/views/sections/detalle.php'; ?>
  <?php include __DIR__ . '/views/sections/negocio.php'; ?>
  <?php include __DIR__ . '/views/sections/admin.php'; ?>

  <!-- Pie de Página -->
  <?php include __DIR__ . '/views/layouts/footer.php'; ?>

  <!-- ============ MODALES ============ -->
  <?php include __DIR__ . '/views/modals/login.php'; ?>
  <?php include __DIR__ . '/views/modals/user_create.php'; ?>
  <?php include __DIR__ . '/views/modals/user_edit.php'; ?>
  <?php include __DIR__ . '/views/modals/user_delete.php'; ?>
  <?php include __DIR__ . '/views/modals/empresa_edit.php'; ?>
  <?php include __DIR__ . '/views/modals/empresa_delete.php'; ?>

  <script>
    window.SPOT_INITIAL_DATA = {
      categorias: <?= json_encode($categoriasActivas, JSON_UNESCAPED_UNICODE) ?>,
      stats: <?= json_encode($statsPublicas, JSON_UNESCAPED_UNICODE) ?>,
      comercios: <?= json_encode($comerciosAprobados, JSON_UNESCAPED_UNICODE) ?>
    };
  </script>
  <!-- Scripts y Librerías -->
  <?php include __DIR__ . '/views/layouts/scripts.php'; ?>
</body>

</html>