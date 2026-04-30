USE dream_colors;

INSERT INTO categoria (tipo)
SELECT 'cabello'
WHERE NOT EXISTS (
    SELECT 1 FROM categoria WHERE tipo = 'cabello'
);

INSERT INTO categoria (tipo)
SELECT 'estetica'
WHERE NOT EXISTS (
    SELECT 1 FROM categoria WHERE tipo = 'estetica'
);

INSERT INTO servicios (id_categoria, nombre, precio)
SELECT categoria.id, servicios_seed.nombre, 0
FROM categoria
JOIN (
    SELECT 'cabello' AS tipo, 'Tratamientos de cuero cabelludo' AS nombre
    UNION ALL SELECT 'cabello', 'Tratamientos de fibra capilar'
    UNION ALL SELECT 'cabello', 'Soluciones avanzadas'
    UNION ALL SELECT 'cabello', 'Corte'
    UNION ALL SELECT 'cabello', 'Color'
    UNION ALL SELECT 'cabello', 'Cambio de forma'
    UNION ALL SELECT 'cabello', 'Peinados'
    UNION ALL SELECT 'estetica', 'Facial'
    UNION ALL SELECT 'estetica', 'Corporal'
    UNION ALL SELECT 'estetica', 'Mirada perfecta'
    UNION ALL SELECT 'estetica', 'Exclusivo hombres'
    UNION ALL SELECT 'estetica', 'Depilación'
    UNION ALL SELECT 'estetica', 'Maquillaje'
    UNION ALL SELECT 'estetica', 'Cuidado uñas'
) AS servicios_seed ON servicios_seed.tipo = categoria.tipo
WHERE NOT EXISTS (
    SELECT 1
    FROM servicios
    WHERE servicios.id_categoria = categoria.id
    AND servicios.nombre = servicios_seed.nombre
);
