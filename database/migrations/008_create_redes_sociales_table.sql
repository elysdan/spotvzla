-- Migración 008: Tabla redes_sociales para el Maestro de Redes Sociales
CREATE TABLE IF NOT EXISTS `redes_sociales` (
  `id` INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  `nombre` VARCHAR(100) NOT NULL,
  `icono` VARCHAR(100) NOT NULL COMMENT 'Clase Font Awesome, ej: fa-brands fa-facebook',
  `url_base` VARCHAR(255) NULL COMMENT 'Prefijo para generar enlace, ej: https://facebook.com/',
  `color` VARCHAR(50) NULL COMMENT 'Color hexadecimal o nombre para hover temático',
  `activo` TINYINT(1) NOT NULL DEFAULT 1,
  `orden` INT NOT NULL DEFAULT 0,
  `created_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
  `updated_at` TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  INDEX `idx_activo_orden` (`activo`, `orden`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

-- Seeding inicial con redes populares y sus iconos oficiales de Font Awesome
INSERT INTO `redes_sociales` (`nombre`, `icono`, `url_base`, `color`, `activo`, `orden`) VALUES
('Instagram', 'fa-brands fa-instagram', 'https://instagram.com/', '#E1306C', 1, 1),
('WhatsApp', 'fa-brands fa-whatsapp', 'https://wa.me/', '#25D366', 1, 2),
('TikTok', 'fa-brands fa-tiktok', 'https://tiktok.com/@', '#000000', 1, 3),
('Sitio Web', 'fa-solid fa-globe', '', '#0F9B8E', 1, 4),
('Facebook', 'fa-brands fa-facebook', 'https://facebook.com/', '#1877F2', 1, 5),
('Telegram', 'fa-brands fa-telegram', 'https://t.me/', '#229ED9', 1, 6),
('YouTube', 'fa-brands fa-youtube', 'https://youtube.com/', '#FF0000', 1, 7),
('Twitter / X', 'fa-brands fa-x-twitter', 'https://x.com/', '#111111', 1, 8),
('LinkedIn', 'fa-brands fa-linkedin-in', 'https://linkedin.com/in/', '#0A66C2', 1, 9),
('Discord', 'fa-brands fa-discord', 'https://discord.gg/', '#5865F2', 1, 10),
('Threads', 'fa-brands fa-threads', 'https://threads.net/@', '#000000', 1, 11),
('Pinterest', 'fa-brands fa-pinterest', 'https://pinterest.com/', '#E60023', 1, 12);
