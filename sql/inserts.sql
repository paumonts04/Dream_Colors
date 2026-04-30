USE dream_colors;

INSERT INTO categoria (id, tipo)
VALUES
    (1, 'cabello'),
    (2, 'estetica')
ON DUPLICATE KEY UPDATE
    tipo = VALUES(tipo);

INSERT INTO servicios (id_categoria, nombre, precio)
VALUES
    (1, 'Tratamientos de cuero cabelludo', 0),
    (1, 'Tratamientos de fibra capilar', 0),
    (1, 'Soluciones avanzadas', 0),
    (1, 'Corte', 0),
    (1, 'Color', 0),
    (1, 'Cambio de forma', 0),
    (1, 'Peinados', 0),
    (2, 'Facial', 0),
    (2, 'Corporal', 0),
    (2, 'Mirada perfecta', 0),
    (2, 'Exclusivo hombres', 0),
    (2, 'Depilación', 0),
    (2, 'Maquillaje', 0),
    (2, 'Cuidado uñas', 0);
