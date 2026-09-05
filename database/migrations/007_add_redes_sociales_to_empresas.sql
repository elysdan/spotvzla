-- Migración 007: Agregar columna de Redes Sociales (JSON) a la tabla empresas
ALTER TABLE `empresas` 
ADD COLUMN `redes_sociales` JSON NULL AFTER `logo_url`;
