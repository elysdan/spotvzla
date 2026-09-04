<?php
/**
 * Runner de Migraciones en PHP Puro (Estilo Laravel)
 * Compatible con CLI y navegador web
 */

require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/database.php';

$isCli = (PHP_SAPI === 'cli');

function out(string $msg, string $type = 'info'): void {
    global $isCli;
    $colors = [
        'info'    => "\033[36m",
        'success' => "\033[32m",
        'warning' => "\033[33m",
        'error'   => "\033[31m",
        'reset'   => "\033[0m"
    ];

    if ($isCli) {
        $color = $colors[$type] ?? $colors['info'];
        echo $color . $msg . $colors['reset'] . PHP_EOL;
    } else {
        $htmlColors = [
            'info'    => '#0F9B8E',
            'success' => '#10B981',
            'warning' => '#F59E0B',
            'error'   => '#EF4444'
        ];
        $c = $htmlColors[$type] ?? '#333';
        echo "<div style='font-family: monospace; color: {$c}; margin: 4px 0;'><strong>[" . strtoupper($type) . "]</strong> " . htmlspecialchars($msg) . "</div>";
        flush();
    }
}

try {
    if (!$isCli) {
        echo "<!DOCTYPE html><html lang='es'><head><meta charset='UTF-8'><title>Migraciones SpotVzla</title><body style='background:#111;color:#eee;padding:2rem;'><h2>Migraciones Spot Venezuela</h2>";
    }

    out("Iniciando runner de migraciones...", "info");

    // 1. Conectar a MySQL sin especificar base de datos para asegurar que exista
    $rootPdo = Database::getConnection('');
    $dbName = DB_NAME;
    
    out("Verificando base de datos '{$dbName}'...", "info");
    $rootPdo->exec("CREATE DATABASE IF NOT EXISTS `{$dbName}` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;");
    out("Base de datos '{$dbName}' lista.", "success");

    // 2. Conectar a la base de datos específica
    $pdo = Database::getConnection($dbName);

    // 3. Crear tabla de control de migraciones si no existe
    $pdo->exec("
        CREATE TABLE IF NOT EXISTS `migrations` (
            `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            `migration` VARCHAR(255) NOT NULL UNIQUE,
            `executed_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
    ");

    // 4. Obtener migraciones ya ejecutadas
    $stmt = $pdo->query("SELECT `migration` FROM `migrations`");
    $executed = $stmt->fetchAll(PDO::FETCH_COLUMN);

    // 5. Buscar archivos .sql en database/migrations/
    $migrationsDir = __DIR__ . '/migrations';
    $files = glob($migrationsDir . '/*.sql');
    sort($files);

    $pendingCount = 0;

    foreach ($files as $file) {
        $filename = basename($file);

        if (in_array($filename, $executed)) {
            continue; // Ya ejecutada
        }

        $pendingCount++;
        out("Ejecutando migración: {$filename} ...", "info");

        $sql = file_get_contents($file);

        // Ejecutar script SQL
        $pdo->exec($sql);

        // Registrar en tabla de migraciones
        $ins = $pdo->prepare("INSERT INTO `migrations` (`migration`) VALUES (:mig)");
        $ins->execute([':mig' => $filename]);

        out("Migración completada con éxito: {$filename}", "success");
    }

    if ($pendingCount === 0) {
        out("Todas las migraciones están al día. No hay cambios pendientes.", "success");
    } else {
        out("Proceso finalizado. Se ejecutaron {$pendingCount} migraciones correctamente.", "success");
    }

    if (!$isCli) {
        echo "<hr><p><a href='../index.php' style='color:#0F9B8E;'>Volver a Spot</a></p></body></html>";
    }

} catch (Throwable $e) {
    out("ERROR FATAL DURANTE LA MIGRACIÓN: " . $e->getMessage(), "error");
    if (!$isCli) {
        echo "</body></html>";
    }
    exit(1);
}
