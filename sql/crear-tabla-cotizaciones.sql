CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    descripcion TEXT,
    precio_minorista DECIMAL(10,2) NOT NULL,
    precio_mayorista DECIMAL(10,2) NOT NULL,
    stock INT DEFAULT 0,
    imagen_url VARCHAR(255),
    creado_en DATETIME DEFAULT CURRENT_TIMESTAMP
);