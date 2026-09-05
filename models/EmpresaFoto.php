<?php
/**
 * Modelo EmpresaFoto
 * Gestión de fotos de la galería (local, fachada, equipo y productos)
 */

require_once __DIR__ . '/../config/database.php';

class EmpresaFoto
{
    /**
     * Obtiene todas las fotos asociadas a una empresa, ordenadas.
     */
    public static function getByEmpresaId(int $empresaId): array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("
            SELECT id, empresa_id, url, titulo, orden, created_at
            FROM empresa_fotos
            WHERE empresa_id = :empresa_id
            ORDER BY orden ASC, id ASC
        ");
        $stmt->execute([':empresa_id' => $empresaId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Obtiene fotos agrupadas por empresa para una lista de IDs (evita N+1).
     */
    public static function getByEmpresas(array $empresaIds): array
    {
        if (empty($empresaIds)) {
            return [];
        }

        $pdo = Database::getConnection();
        $inQuery = implode(',', array_fill(0, count($empresaIds), '?'));
        $stmt = $pdo->prepare("
            SELECT id, empresa_id, url, titulo, orden
            FROM empresa_fotos
            WHERE empresa_id IN ($inQuery)
            ORDER BY empresa_id ASC, orden ASC, id ASC
        ");
        $stmt->execute(array_values($empresaIds));
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $grouped = [];
        foreach ($rows as $r) {
            $eid = (int)$r['empresa_id'];
            if (!isset($grouped[$eid])) {
                $grouped[$eid] = [];
            }
            $grouped[$eid][] = [
                'id'     => (int)$r['id'],
                'url'    => $r['url'],
                'titulo' => $r['titulo'] ?? ''
            ];
        }

        return $grouped;
    }

    /**
     * Agrega una foto a una empresa.
     */
    public static function add(int $empresaId, string $url, ?string $titulo = null, int $orden = 0, ?PDO $pdo = null): int
    {
        $db = $pdo ?? Database::getConnection();
        $stmt = $db->prepare("
            INSERT INTO empresa_fotos (empresa_id, url, titulo, orden)
            VALUES (:empresa_id, :url, :titulo, :orden)
        ");
        $stmt->execute([
            ':empresa_id' => $empresaId,
            ':url'        => trim($url),
            ':titulo'     => !empty($titulo) ? trim($titulo) : null,
            ':orden'      => $orden
        ]);
        return (int)$db->lastInsertId();
    }

    /**
     * Elimina una foto por ID y remueve el archivo físico si está en uploads.
     */
    public static function delete(int $id, ?PDO $pdo = null): bool
    {
        $db = $pdo ?? Database::getConnection();

        $stmtSel = $db->prepare("SELECT url FROM empresa_fotos WHERE id = :id LIMIT 1");
        $stmtSel->execute([':id' => $id]);
        $foto = $stmtSel->fetch(PDO::FETCH_ASSOC);

        if (!$foto) {
            return false;
        }

        $stmtDel = $db->prepare("DELETE FROM empresa_fotos WHERE id = :id");
        $deleted = $stmtDel->execute([':id' => $id]);

        if ($deleted && !empty($foto['url'])) {
            self::deletePhysicalFile($foto['url']);
        }

        return $deleted;
    }

    /**
     * Sincroniza la lista de fotos de una empresa (usado en creación y actualización).
     * $fotos puede ser array de URLs de strings o array de objetos/asoc con 'url' y opcionalmente 'titulo'.
     */
    public static function sync(int $empresaId, array $fotos, ?PDO $pdo = null): void
    {
        $db = $pdo ?? Database::getConnection();

        // 1. Obtener fotos existentes actuales
        $stmt = $db->prepare("SELECT id, url FROM empresa_fotos WHERE empresa_id = :eid");
        $stmt->execute([':eid' => $empresaId]);
        $existing = $stmt->fetchAll(PDO::FETCH_ASSOC);

        $newUrls = [];
        $sanitizedList = [];
        $orden = 1;

        foreach ($fotos as $item) {
            $url = is_array($item) ? trim((string)($item['url'] ?? '')) : trim((string)$item);
            $titulo = is_array($item) ? trim((string)($item['titulo'] ?? '')) : null;

            if ($url !== '') {
                $newUrls[] = $url;
                $sanitizedList[] = [
                    'url'    => $url,
                    'titulo' => !empty($titulo) ? $titulo : null,
                    'orden'  => $orden++
                ];
            }
        }

        // 2. Eliminar fotos que ya no están en la nueva lista
        foreach ($existing as $oldFoto) {
            if (!in_array($oldFoto['url'], $newUrls, true)) {
                self::delete((int)$oldFoto['id'], $db);
            }
        }

        // 3. Insertar fotos que son nuevas
        $existingUrls = array_column($existing, 'url');
        foreach ($sanitizedList as $f) {
            if (!in_array($f['url'], $existingUrls, true)) {
                self::add($empresaId, $f['url'], $f['titulo'], $f['orden'], $db);
            }
        }
    }

    /**
     * Helper para eliminar archivo físico si es un upload local
     */
    private static function deletePhysicalFile(string $url): void
    {
        $cleanUrl = ltrim($url, '/');
        if (str_starts_with($cleanUrl, 'uploads/')) {
            $fullPath = __DIR__ . '/../' . $cleanUrl;
            if (file_exists($fullPath) && is_file($fullPath)) {
                @unlink($fullPath);
            }
        }
    }
}
