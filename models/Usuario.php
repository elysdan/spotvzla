<?php
/**
 * Modelo Usuario
 */

require_once __DIR__ . '/../config/database.php';

class Usuario
{
    public static function findByEmail(string $email): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = :email LIMIT 1");
        $stmt->execute([':email' => strtolower(trim($email))]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function findById(int $id): ?array
    {
        $pdo = Database::getConnection();
        $stmt = $pdo->prepare("SELECT id, nombre, email, telefono, rol, estado, created_at FROM usuarios WHERE id = :id LIMIT 1");
        $stmt->execute([':id' => $id]);
        $user = $stmt->fetch();
        return $user ?: null;
    }

    public static function create(array $data): int
    {
        $pdo = Database::getConnection();
        $sql = "INSERT INTO usuarios (nombre, email, telefono, rol, password_hash, estado) 
                VALUES (:nombre, :email, :telefono, :rol, :hash, :estado)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            ':nombre'   => trim($data['nombre']),
            ':email'    => strtolower(trim($data['email'])),
            ':telefono' => !empty($data['telefono']) ? trim($data['telefono']) : null,
            ':rol'      => in_array($data['rol'] ?? '', ['admin', 'empresa', 'usuario']) ? $data['rol'] : 'empresa',
            ':hash'     => password_hash($data['password'], PASSWORD_DEFAULT),
            ':estado'   => $data['estado'] ?? 'activo'
        ]);

        return (int)$pdo->lastInsertId();
    }

    public static function getAllWithCounts(): array
    {
        $pdo = Database::getConnection();
        $sql = "
            SELECT 
                u.id,
                u.nombre,
                u.email,
                u.telefono,
                u.rol,
                u.estado,
                u.created_at,
                COUNT(e.id) AS total_empresas
            FROM usuarios u
            LEFT JOIN empresas e ON e.usuario_id = u.id
            GROUP BY u.id
            ORDER BY u.id DESC
        ";
        $stmt = $pdo->query($sql);
        return $stmt->fetchAll();
    }

    public static function countAll(): int
    {
        $pdo = Database::getConnection();
        return (int)$pdo->query("SELECT COUNT(*) FROM usuarios")->fetchColumn();
    }
}
