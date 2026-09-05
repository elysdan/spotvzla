<?php
/**
 * Modelo Categoria
 */

require_once __DIR__ . '/../config/database.php';

class Categoria
{
    public static function getAll(): array
    {
        $pdo = Database::getConnection();
        return $pdo->query("SELECT id, slug, nombre, icono, color_gradiente, orden FROM categorias WHERE activo = 1 ORDER BY orden ASC")->fetchAll();
    }

    public static function getActiveWithCounts(bool $onlyWithComercios = true): array
    {
        $pdo = Database::getConnection();
        $sql = "
            SELECT 
                c.id, 
                c.slug AS k, 
                c.nombre AS n, 
                c.icono AS i, 
                c.color_gradiente AS g,
                COUNT(e.id) AS c
            FROM categorias c
            LEFT JOIN empresas e ON e.categoria_id = c.id AND e.estado = 'aprobado'
            WHERE c.activo = 1
            GROUP BY c.id, c.slug, c.nombre, c.icono, c.color_gradiente, c.orden
        ";
        if ($onlyWithComercios) {
            $sql .= " HAVING c > 0 ";
        }
        $sql .= " ORDER BY c.orden ASC, c DESC ";

        $rows = $pdo->query($sql)->fetchAll();
        foreach ($rows as &$r) {
            $r['id'] = (int)$r['id'];
            $r['c']  = (int)$r['c'];
            if (empty($r['g'])) {
                $r['g'] = 'linear-gradient(135deg,#0F9B8E,#0A6E64)';
            }
        }
        unset($r);
        return $rows;
    }

    public static function getSlugToIdMap(): array
    {
        $pdo = Database::getConnection();
        return $pdo->query("SELECT slug, id FROM categorias")->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
