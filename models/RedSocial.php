<?php
/**
 * Modelo RedSocial (Maestro de Redes Sociales)
 */

require_once __DIR__ . '/../config/database.php';

class RedSocial
{
    public static function getAll(bool $onlyActive = true): array
    {
        $pdo = Database::getConnection();
        $sql = "SELECT id, nombre, icono, url_base, color, activo, orden, created_at, updated_at 
                FROM redes_sociales";
        if ($onlyActive) {
            $sql .= " WHERE activo = 1";
        }
        $sql .= " ORDER BY orden ASC, id ASC";

        return $pdo->query($sql)->fetchAll();
    }

    public static function getById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM redes_sociales WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function findByName(string $nombre): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM redes_sociales WHERE LOWER(nombre) = :nombre LIMIT 1");
        $stmt->execute([':nombre' => strtolower(trim($nombre))]);
        $row = $stmt->fetch();
        return $row ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::getConnection();
        $sql = "INSERT INTO redes_sociales (nombre, icono, url_base, color, activo, orden) 
                VALUES (:nombre, :icono, :url_base, :color, :activo, :orden)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre'   => trim($data['nombre']),
            ':icono'    => trim($data['icono']),
            ':url_base' => !empty($data['url_base']) ? trim($data['url_base']) : null,
            ':color'    => !empty($data['color']) ? trim($data['color']) : null,
            ':activo'   => isset($data['activo']) ? (int)$data['activo'] : 1,
            ':orden'    => isset($data['orden']) ? (int)$data['orden'] : 0
        ]);

        return (int)$pdo->lastInsertId();
    }

    public static function update(int $id, array $data): bool
    {
        $pdo = Database::getConnection();
        $sql = "UPDATE redes_sociales 
                SET nombre = :nombre, 
                    icono = :icono, 
                    url_base = :url_base, 
                    color = :color, 
                    activo = :activo, 
                    orden = :orden 
                WHERE id = :id";
        
        $stmt = $pdo->prepare($sql);
        return $stmt->execute([
            ':id'       => $id,
            ':nombre'   => trim($data['nombre']),
            ':icono'    => trim($data['icono']),
            ':url_base' => !empty($data['url_base']) ? trim($data['url_base']) : null,
            ':color'    => !empty($data['color']) ? trim($data['color']) : null,
            ':activo'   => isset($data['activo']) ? (int)$data['activo'] : 1,
            ':orden'    => isset($data['orden']) ? (int)$data['orden'] : 0
        ]);
    }

    public static function delete(int $id): bool
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("DELETE FROM redes_sociales WHERE id = :id");
        return $stmt->execute([':id' => $id]);
    }

    public static function countAll(): int
    {
        $pdo = Database::getConnection();
        return (int)$pdo->query("SELECT COUNT(*) FROM redes_sociales")->fetchColumn();
    }
}
