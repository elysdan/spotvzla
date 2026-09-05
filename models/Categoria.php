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
        return $pdo->query("SELECT id, slug, nombre, icono, color_fondo, orden FROM categorias ORDER BY orden ASC")->fetchAll();
    }

    public static function getSlugToIdMap(): array
    {
        $pdo = Database::getConnection();
        return $pdo->query("SELECT slug, id FROM categorias")->fetchAll(PDO::FETCH_KEY_PAIR);
    }
}
