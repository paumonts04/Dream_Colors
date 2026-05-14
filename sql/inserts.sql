USE dream_colors;

-- =============================================
-- USUARIO ADMINISTRADOR
-- =============================================
INSERT INTO usuarios (telefono, nombre, apellidos, email, password, strikes, rol, block) VALUES
    ('', 'admin', 'admin', 'admin@dreamcolors.local', '$2y$10$Xw4IZrV0p4AbM94cSQv5iuc.a96Hopg5xn3zf.Y.AZf0LFJvDiEBC', 0, 1, 0)
ON DUPLICATE KEY UPDATE nombre = VALUES(nombre);

-- =============================================
-- CATEGORÍAS
-- =============================================
INSERT INTO categoria (id, tipo) VALUES
    (1, 'Peluquería'),
    (2, 'Tratamientos Capilares'),
    (3, 'Estética')
ON DUPLICATE KEY UPDATE tipo = VALUES(tipo);


-- =============================================
-- SERVICIOS – PELUQUERÍA (id_categoria = 1)
-- =============================================
INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion) VALUES
    (1, 'Peinado corto o normal',         'Incluye lavado y acondicionamiento.',                                    15.00,  30),
    (1, 'Peinado XXL',                    'Peinado para cabello extra largo. Incluye lavado y acondicionamiento.', 18.00,  45),
    (1, 'Peinar con difusor',             'Secado trabajado con difusor. Incluye lavado y acondicionamiento.',      8.00,  20),
    (1, 'Corte mujer',                    'Incluye lavado y acondicionamiento.',                                    17.00,  45),
    (1, 'Corte hombre',                   'Incluye lavado y acondicionamiento.',                                    15.00,  30),
    (1, 'Corte niños (hasta 10 años)',    'Incluye lavado y acondicionamiento.',                                    10.00,  20),
    (1, 'Color',                          'Coloración completa. Incluye lavado y acondicionamiento.',               28.00,  60),
    (1, 'Mechas con matiz o color',       'Mechas tradicionales o babylight. Incluye lavado y acondicionamiento.', 50.00,  90),
    (1, 'Balayage y peinar',              'Balayage completo con peinado incluido.',                                90.00, 120),
    (1, 'Moldeado',                       'Incluye lavado y acondicionamiento.',                                    45.00,  90);


-- =============================================
-- SERVICIOS – TRATAMIENTOS CAPILARES (id_categoria = 2)
-- =============================================
INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion) VALUES
    (2, 'Tratamiento de queratina',             'Alisado con queratina. Precio según largo del cabello.',             120.00, 120),
    (2, 'Tratamiento de queratina XXL',         'Alisado con queratina para cabello muy largo.',                      150.00, 150),
    (2, 'Tratamiento de botox capilar',         'Tratamiento regenerador de botox capilar. Incluye lavado.',           60.00,  90),
    (2, 'Extra producto (cada 5g adicionales)', 'Coste adicional por cada 5 gramos extra de producto utilizado.',      3.00,  NULL);


-- =============================================
-- SERVICIOS – ESTÉTICA (id_categoria = 3)
-- =============================================
INSERT INTO servicios (id_categoria, nombre, descripcion, precio, duracion) VALUES
    (3, 'Depilación labio superior',        'Depilación con hilo o cera.',                                          3.00,  10),
    (3, 'Depilación cejas',                 'Depilación y perfilado de cejas.',                                     3.00,  10),
    (3, 'Primera puesta pestañas 1D',       'Extensiones de pestañas efecto natural.',                             45.00,  90),
    (3, 'Primera puesta pestañas 2D',       'Extensiones de pestañas efecto volumen medio.',                       45.00, 100),
    (3, 'Primera puesta pestañas 3D',       'Extensiones de pestañas efecto volumen máximo.',                      45.00, 110),
    (3, 'Primera puesta efecto Kardashian', 'Extensiones de pestañas con efecto Kardashian dramático.',            60.00, 120),
    (3, 'Relleno pestañas 1D / 2D / 3D',   'Relleno antes de 3 semanas desde la primera puesta.',                30.00,  60),
    (3, 'Relleno efecto Kardashian',        'Relleno efecto Kardashian antes de 3 semanas.',                       45.00,  75),
    (3, 'Manicura semipermanente',          'Esmaltado semipermanente en manos.',                                  15.00,  45),
    (3, 'Manicura rubber',                  'Manicura con base rubber de larga duración.',                         22.00,  50),
    (3, 'Acrílico o gel – primera puesta',  'Uñas de acrílico o gel, primera puesta.',                            29.50,  75),
    (3, 'Relleno acrílico o gel',           'Relleno antes de 3 semanas desde la primera puesta.',                22.00,  45),
    (3, 'Pack manos y pies semipermanente', 'Manicura y pedicura semipermanente.',                                 29.50,  80);


-- =============================================
-- BONOS
-- =============================================
INSERT INTO bonos (nombre_bono, precio, cantidad_sesiones_total) VALUES
    ('4 Peinados',                       45.00, 4),
    ('Color + 4 Peinados',               65.00, 5),
    ('Mechas o Babylight + 4 Peinados',  80.00, 5),
    ('Balayage con matiz + 4 Peinados', 120.00, 5);


-- =============================================
-- PROMOCIONES
-- =============================================
-- Lunes: queratina 120€ → 100€ (≈17% descuento)
INSERT INTO promociones (fecha_inicio, fecha_final, porcentaje) VALUES
    ('2025-01-06 00:00:00', '2025-12-29 23:59:59', 17);

INSERT INTO promociones_servicios (id_promocion, id_servicio)
SELECT 1, id FROM servicios WHERE nombre = 'Tratamiento de queratina';

-- Miércoles: botox 60€ → 48€ (20% descuento)
INSERT INTO promociones (fecha_inicio, fecha_final, porcentaje) VALUES
    ('2025-01-08 00:00:00', '2025-12-31 23:59:59', 20);

INSERT INTO promociones_servicios (id_promocion, id_servicio)
SELECT 2, id FROM servicios WHERE nombre = 'Tratamiento de botox capilar';


-- =============================================
-- HORARIOS
-- =============================================
INSERT INTO horarios (dia_semana, hora_inicio, hora_cierre, activo) VALUES
    (1, '09:00:00', '20:00:00', 1),  -- Lunes
    (2, '09:00:00', '20:00:00', 1),  -- Martes
    (3, '09:00:00', '20:00:00', 1),  -- Miércoles
    (4, '09:00:00', '20:00:00', 1),  -- Jueves
    (5, '09:00:00', '20:00:00', 1),  -- Viernes
    (6, '09:00:00', '14:00:00', 1),  -- Sábado (media jornada)
    (0, '00:00:00', '00:00:00', 0);  -- Domingo (cerrado)


