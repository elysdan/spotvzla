-- Migración 006: Datos iniciales (Admin por defecto, categorías, métodos de pago y comercios base)

-- 1. Usuario Administrador por defecto (Contraseña: Admin123*)
INSERT INTO `usuarios` (`id`, `nombre`, `email`, `password_hash`, `telefono`, `rol`, `estado`)
VALUES (
    1,
    'Administrador Spot',
    'admin@spotvzla.com',
    '$2y$10$097Ebvmfw9ASFyX6Vl64OOCG2iJ5NBbYpKpH97XC5ZZYgc24aQZs2',
    '04120000000',
    'admin',
    'activo'
) ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

-- 2. Categorías
INSERT INTO `categorias` (`id`, `slug`, `nombre`, `icono`, `color_gradiente`, `orden`) VALUES
(1, 'restaurante', 'Restaurantes', 'i-restaurante', 'linear-gradient(135deg,#F25C54,#C0392B)', 1),
(2, 'cafe', 'Cafés', 'i-cafe', 'linear-gradient(135deg,#B07A4E,#6E4426)', 2),
(3, 'panaderia', 'Panaderías', 'i-panaderia', 'linear-gradient(135deg,#E8A93F,#B06E14)', 3),
(4, 'supermercado', 'Supermercados', 'i-supermercado', 'linear-gradient(135deg,#3FA96B,#1E6B41)', 4),
(5, 'hotel', 'Hoteles', 'i-hotel', 'linear-gradient(135deg,#4A7FD6,#22417F)', 5),
(6, 'tienda', 'Tiendas', 'i-tienda', 'linear-gradient(135deg,#E0703F,#A2411A)', 6),
(7, 'entretenimiento', 'Entretenimiento', 'i-entretenimiento', 'linear-gradient(135deg,#9A5ED6,#5A2C8F)', 7),
(8, 'servicios', 'Servicios', 'i-servicios', 'linear-gradient(135deg,#4F9FB5,#22606F)', 8),
(9, 'tecnologia', 'Tecnología', 'i-tecnologia', 'linear-gradient(135deg,#3D8F97,#1E5054)', 9)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

-- 3. Métodos de Pago
INSERT INTO `metodos_pago` (`id`, `slug`, `nombre`, `letra_badge`, `color_badge`, `orden`) VALUES
(1, 'cashea', 'Cashea', 'C', '#F59E0B', 1),
(2, 'zelle', 'Zelle', 'Z', '#7414CA', 2),
(3, 'zinlli', 'Zinlli', 'Z', '#A855F7', 3),
(4, 'paypal', 'PayPal', 'P', '#0070BA', 4),
(5, 'movil', 'Pago Móvil', 'PM', '#06B6D4', 5),
(6, 'punto', 'Punto', 'P', '#64748B', 6),
(7, 'binance', 'Binance', 'B', '#C98A00', 7),
(8, 'efectivo', 'Efectivo', '$', '#10B981', 8)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

-- 4. Comercios Semilla (migración de los datos estáticos de index.php)
INSERT INTO `empresas` (`id`, `usuario_id`, `nombre`, `rif`, `categoria_id`, `descripcion`, `telefono`, `correo_contacto`, `direccion`, `zona`, `latitud`, `longitud`, `estado`, `calificacion`, `total_resenas`, `rango_precio`, `delivery`, `abierto`, `verificado`) VALUES
(1, 1, 'La Cocina de Mamá', 'J-30123456-1', 1, 'Comida criolla casera con almuerzos ejecutivos de lunes a viernes. Postres de la casa y ambiente familiar.', '04121112233', 'contacto@lacocinademama.com', 'Av. Francisco de Miranda', 'Chacao', 10.49750000, -66.85420000, 'aprobado', 4.9, 312, '$$', 1, 1, 1),
(2, 1, 'Café Arábica', 'J-30123456-2', 2, 'Tostadores propios de café venezolano de altura. Buen wifi y mesas para trabajar toda la tarde.', '04142223344', 'hola@cafearabica.com', 'Calle París', 'Las Mercedes', 10.48420000, -66.86170000, 'aprobado', 4.6, 198, '$$', 0, 1, 1),
(3, 1, 'Panadería La Espiga', 'J-30123456-3', 3, 'Pan salido del horno cada dos horas, cachitos rellenos y golfeados. Cola corta antes de las 8 de la mañana.', '04163334455', 'pedidos@laespiga.com', 'Av. Luis Roche', 'Altamira', 10.49540000, -66.84620000, 'aprobado', 4.7, 421, '$', 1, 1, 1),
(4, 1, 'Abasto Express', 'J-30123456-4', 4, 'Abasto de barrio con verdulería y charcutería. Reparte en la zona hasta las 7 de la noche.', '04244445566', 'ventas@abastoexpress.com', 'Av. Principal', 'La Castellana', 10.49860000, -66.85060000, 'aprobado', 4.3, 156, '$$', 1, 1, 1),
(5, 1, 'Hotel Ávila Suites', 'J-30123456-5', 5, 'Habitaciones con vista al Ávila, desayuno incluido y estacionamiento propio. Tarifas en divisas.', '02125556677', 'reservas@avilasunites.com', 'Av. Vollmer', 'San Bernardino', 10.51220000, -66.89180000, 'aprobado', 4.5, 88, '$$$', 0, 1, 1),
(6, 1, 'TecnoMundo', 'J-30123456-6', 9, 'Repuestos, accesorios y servicio técnico de celulares y laptops. Garantía de 30 días por escrito.', '04126667788', 'soporte@tecnomundo.com', 'Bulevar de Sabana Grande', 'Sabana Grande', 10.49190000, -66.87370000, 'aprobado', 4.1, 64, '$$', 0, 0, 1)
ON DUPLICATE KEY UPDATE `nombre` = VALUES(`nombre`);

-- 5. Métodos de Pago asociados a Comercios Semilla
-- La Cocina de Mamá: cashea (1), punto (6), efectivo (8), movil (5)
INSERT IGNORE INTO `empresa_metodos_pago` (`empresa_id`, `metodo_pago_id`) VALUES
(1, 1), (1, 6), (1, 8), (1, 5),
-- Café Arábica: zelle (2), punto (6), binance (7), efectivo (8)
(2, 2), (2, 6), (2, 7), (2, 8),
-- Panadería La Espiga: cashea (1), movil (5), punto (6), efectivo (8)
(3, 1), (3, 5), (3, 6), (3, 8),
-- Abasto Express: punto (6), movil (5), efectivo (8), zelle (2)
(4, 6), (4, 5), (4, 8), (4, 2),
-- Hotel Ávila Suites: zelle (2), binance (7), punto (6)
(5, 2), (5, 7), (5, 6),
-- TecnoMundo: zelle (2), binance (7), punto (6), efectivo (8)
(6, 2), (6, 7), (6, 6), (6, 8);
