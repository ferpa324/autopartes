-- =====================================================================
-- AutoMarket - base de datos para InfinityFree
-- Importar en phpMyAdmin con la base if0_... ya seleccionada.
-- ADVERTENCIA: borra y recrea las tablas.
-- =====================================================================
DROP TABLE IF EXISTS pedido_detalles;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    rol ENUM('cliente','admin') NOT NULL DEFAULT 'cliente',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE productos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT,
    precio DECIMAL(12,2) NOT NULL,
    stock INT NOT NULL DEFAULT 0,
    tipo ENUM('auto','repuesto') NOT NULL,
    marca VARCHAR(80) NOT NULL,
    modelo VARCHAR(120) NOT NULL,
    kilometros INT NOT NULL DEFAULT 0,
    imagen VARCHAR(255) DEFAULT NULL,
    activo TINYINT(1) NOT NULL DEFAULT 1,
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    actualizado_en TIMESTAMP NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_nombre (nombre),
    INDEX idx_marca (marca),
    INDEX idx_modelo (modelo),
    INDEX idx_activo (activo)
) ENGINE=InnoDB;

CREATE TABLE pedidos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    usuario_id INT NOT NULL,
    total DECIMAL(12,2) NOT NULL,
    estado VARCHAR(30) NOT NULL DEFAULT 'pendiente',
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pedidos_usuario FOREIGN KEY (usuario_id) REFERENCES usuarios(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

CREATE TABLE pedido_detalles (
    id INT AUTO_INCREMENT PRIMARY KEY,
    pedido_id INT NOT NULL,
    producto_id INT NOT NULL,
    cantidad INT NOT NULL,
    precio DECIMAL(12,2) NOT NULL,
    CONSTRAINT fk_detalle_pedido FOREIGN KEY (pedido_id) REFERENCES pedidos(id)
        ON UPDATE CASCADE ON DELETE CASCADE,
    CONSTRAINT fk_detalle_producto FOREIGN KEY (producto_id) REFERENCES productos(id)
        ON UPDATE CASCADE ON DELETE RESTRICT
) ENGINE=InnoDB;

-- AUTOS (solo los que tienen imagen en img/autos/)
INSERT INTO productos (nombre, descripcion, precio, stock, tipo, marca, modelo, kilometros, imagen) VALUES
('Toyota Corolla XEI 2022', 'Sedán automático, cómodo y equipado para uso diario.', 18500000, 1, 'auto', 'Toyota', 'Corolla', 42000, 'img/autos/toyota-corolla.jpg'),
('Ford Ranger XLT 2023', 'Pick-up doble cabina con excelente capacidad de carga.', 29500000, 1, 'auto', 'Ford', 'Ranger', 31000, 'img/autos/ford-rangerXLT.jpg'),
('Volkswagen Golf 1.4 TSI 2021', 'Hatchback turbo con muy buen equipamiento.', 21000000, 1, 'auto', 'Volkswagen', 'Golf', 52000, 'img/autos/volkswagen-golf.jpg'),
('Chevrolet Cruze Premier 2020', 'Sedán turbo con interior confortable y completo.', 17200000, 1, 'auto', 'Chevrolet', 'Cruze', 68000, 'img/autos/chevrolet-cruze.jpg'),
('Fiat Cronos Precision 2023', 'Sedán nacional con buen espacio interior y bajo consumo.', 16800000, 1, 'auto', 'Fiat', 'Cronos', 38000, 'img/autos/fiat-cronos.jpg'),
('Fiat Argo Trekking 2022', 'Hatchback versátil para ciudad y ruta.', 14900000, 1, 'auto', 'Fiat', 'Argo', 44000, 'img/autos/fiat-argo.jpg'),
('Fiat Pulse Drive 2023', 'SUV compacta con buen equipamiento.', 19200000, 1, 'auto', 'Fiat', 'Pulse', 29000, 'img/autos/fiat-pulse.jpg'),
('Renault Clio Mio 2016', 'Compacto urbano, económico y práctico.', 9800000, 1, 'auto', 'Renault', 'Clio', 92000, 'img/autos/renault-clio.jpg'),
('Renault Sandero Intens 2021', 'Hatchback espacioso y cómodo para uso diario.', 13600000, 1, 'auto', 'Renault', 'Sandero', 51000, 'img/autos/renault-sandero.jpg'),
('Renault Duster Iconic 2022', 'SUV con buena altura al suelo y gran baúl.', 20500000, 1, 'auto', 'Renault', 'Duster', 47000, 'img/autos/renault-duster.jpg');

-- REPUESTOS
INSERT INTO productos (nombre, descripcion, precio, stock, tipo, marca, modelo, kilometros, imagen) VALUES
('Pastillas de freno delanteras', 'Juego de pastillas para freno delantero.', 45000, 15, 'repuesto', 'Multimarca', 'Universal', 0, NULL),
('Batería 12V 75Ah', 'Batería para vehículos medianos y grandes.', 180000, 10, 'repuesto', 'Multimarca', 'Universal', 0, NULL),
('Filtro de aceite', 'Filtro de aceite para motor.', 12000, 30, 'repuesto', 'Multimarca', 'Universal', 0, NULL),
('Juego de bujías', 'Juego de bujías para motor naftero.', 35000, 20, 'repuesto', 'Multimarca', 'Universal', 0, NULL),
('Filtro de aire', 'Filtro de aire para mantenimiento preventivo.', 22000, 18, 'repuesto', 'Multimarca', 'Universal', 0, NULL),
('Correa de distribución', 'Kit de correa para distintos motores.', 95000, 8, 'repuesto', 'Multimarca', 'Universal', 0, NULL),
('Cremallera de dirección Renault Clio', 'Cremallera de dirección compatible con Renault Clio.', 185000, 4, 'repuesto', 'Renault', 'Clio', 0, NULL),
('Amortiguador delantero Renault Clio', 'Amortiguador delantero para Renault Clio.', 98000, 8, 'repuesto', 'Renault', 'Clio', 0, NULL),
('Kit de embrague Renault Clio', 'Kit de embrague compatible con Renault Clio.', 210000, 5, 'repuesto', 'Renault', 'Clio', 0, NULL),
('Pastillas de freno Renault Clio', 'Pastillas delanteras compatibles con Renault Clio.', 52000, 12, 'repuesto', 'Renault', 'Clio', 0, NULL),
('Filtro de aire Renault Clio', 'Filtro de aire compatible con Renault Clio.', 24000, 15, 'repuesto', 'Renault', 'Clio', 0, NULL),
('Correa de distribución Renault Clio', 'Kit de distribución compatible con Renault Clio.', 112000, 6, 'repuesto', 'Renault', 'Clio', 0, NULL),
('Cremallera de dirección Toyota Corolla', 'Cremallera compatible con Toyota Corolla.', 245000, 3, 'repuesto', 'Toyota', 'Corolla', 0, NULL),
('Pastillas de freno Toyota Hilux', 'Pastillas delanteras para Toyota Hilux.', 88000, 9, 'repuesto', 'Toyota', 'Hilux', 0, NULL),
('Filtro de aceite Toyota Corolla', 'Filtro de aceite compatible con Toyota Corolla.', 16000, 18, 'repuesto', 'Toyota', 'Corolla', 0, NULL),
('Amortiguador delantero Ford Ranger', 'Amortiguador delantero para Ford Ranger.', 145000, 6, 'repuesto', 'Ford', 'Ranger', 0, NULL),
('Kit de distribución Ford EcoSport', 'Kit de distribución compatible con Ford EcoSport.', 125000, 5, 'repuesto', 'Ford', 'EcoSport', 0, NULL),
('Pastillas de freno Ford Focus', 'Pastillas delanteras compatibles con Ford Focus.', 62000, 10, 'repuesto', 'Ford', 'Focus', 0, NULL),
('Bomba de agua Volkswagen Golf', 'Bomba de agua compatible con Volkswagen Golf.', 92000, 7, 'repuesto', 'Volkswagen', 'Golf', 0, NULL),
('Filtro de habitáculo Volkswagen Polo', 'Filtro de aire de habitáculo para Volkswagen Polo.', 28000, 12, 'repuesto', 'Volkswagen', 'Polo', 0, NULL),
('Amortiguadores Volkswagen Amarok', 'Par de amortiguadores delanteros para Volkswagen Amarok.', 175000, 4, 'repuesto', 'Volkswagen', 'Amarok', 0, NULL),
('Pastillas de freno Chevrolet Cruze', 'Pastillas delanteras compatibles con Chevrolet Cruze.', 68000, 10, 'repuesto', 'Chevrolet', 'Cruze', 0, NULL),
('Filtro de aire Chevrolet Onix', 'Filtro de aire compatible con Chevrolet Onix.', 26000, 14, 'repuesto', 'Chevrolet', 'Onix', 0, NULL),
('Kit de embrague Chevrolet S10', 'Kit de embrague compatible con Chevrolet S10.', 265000, 3, 'repuesto', 'Chevrolet', 'S10', 0, NULL),
('Pastillas de freno Peugeot 208', 'Pastillas delanteras compatibles con Peugeot 208.', 59000, 10, 'repuesto', 'Peugeot', '208', 0, NULL),
('Filtro de aceite Peugeot 2008', 'Filtro de aceite compatible con Peugeot 2008.', 18000, 16, 'repuesto', 'Peugeot', '2008', 0, NULL),
('Amortiguador delantero Fiat Cronos', 'Amortiguador delantero para Fiat Cronos.', 87000, 8, 'repuesto', 'Fiat', 'Cronos', 0, NULL),
('Kit de embrague Fiat Argo', 'Kit de embrague compatible con Fiat Argo.', 205000, 5, 'repuesto', 'Fiat', 'Argo', 0, NULL),
('Filtro de aire Fiat Pulse', 'Filtro de aire compatible con Fiat Pulse.', 25000, 13, 'repuesto', 'Fiat', 'Pulse', 0, NULL),
('Pastillas de freno Citroen C4 Cactus', 'Pastillas delanteras para Citroen C4 Cactus.', 61000, 8, 'repuesto', 'Citroen', 'C4 Cactus', 0, NULL),
('Bomba de combustible Nissan Kicks', 'Bomba de combustible compatible con Nissan Kicks.', 168000, 4, 'repuesto', 'Nissan', 'Kicks', 0, NULL),
('Filtro de aire Nissan Frontier', 'Filtro de aire compatible con Nissan Frontier.', 39000, 9, 'repuesto', 'Nissan', 'Frontier', 0, NULL),
('Pastillas de freno Honda Civic', 'Pastillas delanteras compatibles con Honda Civic.', 72000, 7, 'repuesto', 'Honda', 'Civic', 0, NULL),
('Filtro de habitáculo Honda HR-V', 'Filtro de habitáculo para Honda HR-V.', 31000, 11, 'repuesto', 'Honda', 'HR-V', 0, NULL),
('Pastillas de freno Jeep Renegade', 'Pastillas delanteras compatibles con Jeep Renegade.', 76000, 6, 'repuesto', 'Jeep', 'Renegade', 0, NULL),
('Filtro de aceite Hyundai Creta', 'Filtro de aceite compatible con Hyundai Creta.', 21000, 13, 'repuesto', 'Hyundai', 'Creta', 0, NULL);

-- =====================================================================
-- ADMINISTRADOR
-- 1) Registrate normalmente en /autopartes/registro.php
-- 2) Ejecutá esta línea con TU email para convertirte en admin:
--
--    UPDATE usuarios SET rol = 'admin' WHERE email = 'tu@email.com';
-- =====================================================================
