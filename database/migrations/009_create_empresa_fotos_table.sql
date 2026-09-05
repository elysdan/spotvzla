-- Migración 009: Tabla de Fotos de Comercios / Galería del Local y Equipo
CREATE TABLE IF NOT EXISTS `empresa_fotos` (
    `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
    `empresa_id` INT UNSIGNED NOT NULL,
    `url` VARCHAR(255) NOT NULL,
    `titulo` VARCHAR(150) NULL,
    `orden` INT UNSIGNED NOT NULL DEFAULT 0,
    `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX `idx_empresa_fotos_empresa` (`empresa_id`),
    CONSTRAINT `fk_empresa_fotos_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Fotos iniciales de muestra para comercios base existentes
-- 1. La Cocina de Mamá (Restaurante criollo: fachada, salón, equipo en cocina, plato de la casa)
INSERT INTO `empresa_fotos` (`empresa_id`, `url`, `titulo`, `orden`) VALUES
(1, 'https://images.unsplash.com/photo-1517248135467-4c7edcad34c4?w=900&auto=format&fit=crop&q=80', 'Salón principal y ambiente familiar', 1),
(1, 'https://images.unsplash.com/photo-1555396273-367ea4eb4db5?w=900&auto=format&fit=crop&q=80', 'Nuestros platos criollos y almuerzos', 2),
(1, 'https://images.unsplash.com/photo-1577219491135-ce391730fb2c?w=900&auto=format&fit=crop&q=80', 'Nuestro equipo de cocina preparando el menú', 3),
(1, 'https://images.unsplash.com/photo-1552566626-52f8b828add9?w=900&auto=format&fit=crop&q=80', 'Área de mesas exteriores', 4);

-- 2. Café Arábica (Cafetería de especialidad)
INSERT INTO `empresa_fotos` (`empresa_id`, `url`, `titulo`, `orden`) VALUES
(2, 'https://images.unsplash.com/photo-1501339847302-ac426a4a7cbb?w=900&auto=format&fit=crop&q=80', 'Barra de baristas y tostador', 1),
(2, 'https://images.unsplash.com/photo-1495474472287-4d71bcdd2085?w=900&auto=format&fit=crop&q=80', 'Café de especialidad venezolano', 2),
(2, 'https://images.unsplash.com/photo-1442512595331-e89e73853f31?w=900&auto=format&fit=crop&q=80', 'Espacio de trabajo y wifi rápido', 3);

-- 3. Panadería La Espiga
INSERT INTO `empresa_fotos` (`empresa_id`, `url`, `titulo`, `orden`) VALUES
(3, 'https://images.unsplash.com/photo-1509440159596-0249088772ff?w=900&auto=format&fit=crop&q=80', 'Mostrador de pan recién horneado', 1),
(3, 'https://images.unsplash.com/photo-1586985289688-ca3cf47d3e6e?w=900&auto=format&fit=crop&q=80', 'Nuestros maestros panaderos trabajando', 2);
