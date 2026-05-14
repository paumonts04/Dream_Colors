CREATE DATABASE IF NOT EXISTS dream_colors;
USE dream_colors;

-- 1. USUARIOS
CREATE TABLE usuarios(
    id INT PRIMARY KEY AUTO_INCREMENT,
    telefono CHAR(9),
    nombre VARCHAR(50) NOT NULL,
    apellidos VARCHAR(50),
    email VARCHAR(100) UNIQUE NOT NULL,
    password VARCHAR(300) NOT NULL,
    strikes TINYINT UNSIGNED DEFAULT 0,
    rol BOOLEAN DEFAULT 0,
    block BOOLEAN DEFAULT 0
);

-- 2. CATEGORÍAS Y SERVICIOS
CREATE TABLE categoria(
    id INT PRIMARY KEY AUTO_INCREMENT,
    tipo VARCHAR(50) NOT NULL
);

CREATE TABLE servicios(
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_categoria INT,
    nombre VARCHAR(50) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(7,2) NOT NULL,
    duracion SMALLINT UNSIGNED,
    imagen VARCHAR(255),
    video VARCHAR(255),
    FOREIGN KEY (id_categoria) REFERENCES categoria(id) ON DELETE SET NULL
);

-- 3. BONOS (antes que citas)
CREATE TABLE bonos(
    id INT PRIMARY KEY AUTO_INCREMENT,
    nombre_bono VARCHAR(50) NOT NULL,
    precio DECIMAL(7,2) NOT NULL,
    cantidad_sesiones_total INT NOT NULL
);

CREATE TABLE bonos_usuario(
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT,
    id_bono INT,
    sesiones_restantes INT,
    fecha_compra TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_bono) REFERENCES bonos(id) ON DELETE CASCADE
);

-- 4. CITAS (después de bonos_usuario)
CREATE TABLE citas(
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_usuario INT,
    id_servicio INT,
    id_bono_usuario INT DEFAULT NULL,
    fecha DATE NOT NULL,
    hora TIME NOT NULL,
    estado ENUM('confirmada', 'completada', 'cancelada') DEFAULT 'confirmada',
    fecha_cancelacion DATETIME DEFAULT NULL,
    FOREIGN KEY (id_usuario) REFERENCES usuarios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_servicio) REFERENCES servicios(id) ON DELETE CASCADE,
    FOREIGN KEY (id_bono_usuario) REFERENCES bonos_usuario(id) ON DELETE SET NULL
);

-- 5. PROMOCIONES
CREATE TABLE promociones(
    id INT PRIMARY KEY AUTO_INCREMENT,
    fecha_inicio DATETIME,
    fecha_final DATETIME,
    porcentaje INT CHECK (porcentaje BETWEEN 0 AND 100)
);

CREATE TABLE promociones_servicios(
    id INT PRIMARY KEY AUTO_INCREMENT,
    id_promocion INT,
    id_servicio INT,
    FOREIGN KEY (id_promocion) REFERENCES promociones(id) ON DELETE CASCADE,
    FOREIGN KEY (id_servicio) REFERENCES servicios(id) ON DELETE CASCADE
);

-- 6. HORARIOS Y DISPONIBILIDAD
CREATE TABLE horarios(
    id INT PRIMARY KEY AUTO_INCREMENT,
    dia_semana TINYINT CHECK (dia_semana BETWEEN 0 AND 6),
    hora_inicio TIME,
    hora_cierre TIME,
    activo BOOLEAN DEFAULT 1
);

CREATE TABLE excepciones(
    id INT PRIMARY KEY AUTO_INCREMENT,
    motivo VARCHAR(100),
    fecha_inicio DATE,
    fecha_final DATE
);

CREATE TABLE hora_especial(
    id INT PRIMARY KEY AUTO_INCREMENT,
    fecha DATE,
    hora_inicio TIME,
    hora_final TIME,
    motivo VARCHAR(50)
);