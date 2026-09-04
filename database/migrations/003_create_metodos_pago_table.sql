-- Migración 003: Tabla de Métodos de Pago
CREATE TABLE IF NOT EXISTS `metodos_pago` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `slug` VARCHAR(30) NOT NULL UNIQUE,
    `nombre` VARCHAR(50) NOT NULL,
    `letra_badge` VARCHAR(10) NOT NULL,
    `color_badge` VARCHAR(20) NOT NULL,
    `orden` INT NOT NULL DEFAULT 0,
    `activo` TINYINT(1) NOT NULL DEFAULT 1,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_metodos_slug` (`slug`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
