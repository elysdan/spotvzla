<?php
/**
 * Modelo MetodoPago
 */

require_once __DIR__ . '/../config/database.php';

class MetodoPago
{
    public static function getAll(): array
    {
        $pdo = Database::getConnection();
        return $pdo->query("SELECT id, slug, nombre, insignia, color, orden FROM metodos_pago ORDER BY orden ASC")->fetchAll();
    }

    public static function getSlugToIdMap(): array
    {
        $pdo = Database::getConnection();
        return $pdo->query("SELECT slug, id FROM metodos_pago")->fetchAll(PDO::FETCH_KEY_PAIR);
    }

    public static function syncEmpresaMetodos(int $empresaId, array $metodosPago, ?PDO $pdo = null): void
    {
        if ($pdo === null) {
            $pdo = Database::getConnection();
        }

        // Eliminar asociaciones existentes
        $delStmt = $pdo->prepare("DELETE FROM empresa_metodos_pago WHERE empresa_id = :id");
        $delStmt->execute([':id' => $empresaId]);

        if (empty($metodosPago)) {
            return;
        }

        $slugToId = self::getSlugToIdMap();
        $insStmt = $pdo->prepare("INSERT IGNORE INTO empresa_metodos_pago (empresa_id, metodo_pago_id) VALUES (:eid, :pid)");

        foreach ($metodosPago as $pago) {
            $metodoId = null;
            if (is_numeric($pago)) {
                $metodoId = (int)$pago;
            } elseif (isset($slugToId[$pago])) {
                $metodoId = (int)$slugToId[$pago];
            }

            if ($metodoId !== null) {
                $insStmt->execute([':eid' => $empresaId, ':pid' => $metodoId]);
            }
        }
    }
}
