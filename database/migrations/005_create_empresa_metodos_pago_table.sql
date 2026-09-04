-- Migración 005: Tabla intermedia para Métodos de Pago por Empresa (M:N)
CREATE TABLE IF NOT EXISTS `empresa_metodos_pago` (
    `empresa_id` INT UNSIGNED NOT NULL,
    `metodo_pago_id` INT UNSIGNED NOT NULL,
    PRIMARY KEY (`empresa_id`, `metodo_pago_id`),
    CONSTRAINT `fk_emp_pago_empresa` FOREIGN KEY (`empresa_id`) REFERENCES `empresas` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
    CONSTRAINT `fk_emp_pago_metodo` FOREIGN KEY (`metodo_pago_id`) REFERENCES `metodos_pago` (`id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
