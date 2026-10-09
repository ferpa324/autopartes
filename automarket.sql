CREATE DATABASE IF NOT EXISTS automarket
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE automarket;

DROP TABLE IF EXISTS pedido_detalles;
DROP TABLE IF EXISTS pedidos;
DROP TABLE IF EXISTS productos;
DROP TABLE IF EXISTS usuarios;

CREATE TABLE usuarios (
    id INT AUTO_INCREMENT PRIMARY KEY,
    nombre VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
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
    creado_en TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_tipo (tipo),
    INDEX idx_nombre (nombre),
    INDEX idx_marca (marca),
    INDEX idx_modelo (modelo)
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

INSERT INTO productos (nombre, descripcion, precio, stock, tipo, marca, modelo, kilometros, imagen) VALUES
-- AUTOS ORIGINALES
('Toyota Corolla XEI 2022', 'Sedán automático, cómodo y equipado para uso diario.', 18500000, 1, 'auto', 'Toyota', 'Corolla', 42000, "img/autos/toyota-corolla.jpg");
('Ford Ranger XLT 2023', 'Pick-up doble cabina con excelente capacidad de carga.', 29500000, 1, 'auto', 'Ford', 'Ranger', 31000, "img/autos/ford-rangerXLT.jpg");
('Volkswagen Golf 1.4 TSI 2021', 'Hatchback turbo con muy buen equipamiento.', 21000000, 1, 'auto', 'Volkswagen', 'Golf', 52000, "img/autos/volkswagen-golf.jpg");
('Chevrolet Cruze Premier 2020', 'Sedán turbo con interior confortable y completo.', 17200000, 1, 'auto', 'Chevrolet', 'Cruze', 68000, "img/autos/chevrolet-cruze.jpg");

-- 30 AUTOS NUEVOS
('Fiat Cronos Precision 2023', 'Sedán nacional con buen espacio interior y bajo consumo.', 16800000, 1, 'auto', 'Fiat', 'Cronos', 38000, "img/autos/fiat-cronos.jpg");
('Fiat Argo Trekking 2022', 'Hatchback versátil para ciudad y ruta.', 14900000, 1, 'auto', 'Fiat', 'Argo', 44000, "img/autos/fiat-argo.jpg");
('Fiat Pulse Drive 2023', 'SUV compacta con buen equipamiento.', 19200000, 1, 'auto', 'Fiat', 'Pulse', 29000, "img/auos/fiat-pulse.jpg");
('Renault Clio Mio 2016', 'Compacto urbano, económico y práctico.', 9800000, 1, 'auto', 'Renault', 'Clio', 92000, "img/autos/renault-clio.jpg");
('Renault Sandero Intens 2021', 'Hatchback espacioso y cómodo para uso diario.', 13600000, 1, 'auto', 'Renault', 'Sandero', 51000, "img/autos/renault-sandero.jpg");
('Renault Duster Iconic 2022', 'SUV con buena altura al suelo y gran baúl.', 20500000, 1, 'auto', 'Renault', 'Duster', 47000, 
('Renault Logan Life 2020', 'Sedán amplio y de mantenimiento sencillo.', 11800000, 1, 'auto', 'Renault', 'Logan', 73000, ,
('Peugeot 208 Feline 2023', 'Hatchback moderno con buen equipamiento.', 17800000, 1, 'auto', 'Peugeot', '208', 26000, 
('Peugeot 2008 Allure 2022', 'SUV compacta con diseño moderno.', 21400000, 1, 'auto', 'Peugeot', '2008', 39000, 
('Citroen C4 Cactus Feel 2021', 'SUV compacta confortable para ciudad y ruta.', 15900000, 1, 'auto', 'Citroen', 'C4 Cactus', 56000, 
('Toyota Hilux SR 2022', 'Pick-up robusta para trabajo y uso particular.', 31800000, 1, 'auto', 'Toyota', 'Hilux', 46000, 
('Toyota Yaris XLS 2023', 'Hatchback eficiente con buen equipamiento.', 17100000, 1, 'auto', 'Toyota', 'Yaris', 22000, ,
('Toyota Etios XLS 2021', 'Compacto confiable para uso diario.', 12900000, 1, 'auto', 'Toyota', 'Etios', 61000, 
('Ford Ka Freestyle 2019', 'Compacto urbano con posición de manejo elevada.', 11300000, 1, 'auto', 'Ford', 'Ka', 70000,
('Ford EcoSport Titanium 2020', 'SUV compacta con buen equipamiento.', 14600000, 1, 'auto', 'Ford', 'EcoSport', 65000, 
('Ford Territory SEL 2023', 'SUV mediana con amplio espacio interior.', 28700000, 1, 'auto', 'Ford', 'Territory', 27000, 
('Chevrolet Onix Premier 2022', 'Hatchback turbo, cómodo y tecnológico.', 16200000, 1, 'auto', 'Chevrolet', 'Onix', 41000, ,
('Chevrolet Tracker Premier 2023', 'SUV compacta con motor turbo.', 22100000, 1, 'auto', 'Chevrolet', 'Tracker', 30000, 
('Chevrolet S10 LTZ 2021', 'Pick-up doble cabina para trabajo y viajes.', 27600000, 1, 'auto', 'Chevrolet', 'S10', 59000, 
('Volkswagen T-Cross Highline 2023', 'SUV compacta con gran equipamiento.', 22400000, 1, 'auto', 'Volkswagen', 'T-Cross', 24000, 
('Volkswagen Amarok Comfortline 2022', 'Pick-up potente y cómoda para ruta.', 31900000, 1, 'auto', 'Volkswagen', 'Amarok', 43000, 
('Volkswagen Polo Highline 2023', 'Hatchback con buen desempeño y tecnología.', 16900000, 1, 'auto', 'Volkswagen', 'Polo', 21000, 
('Nissan Kicks Exclusive 2022', 'SUV compacta cómoda y eficiente.', 19900000, 1, 'auto', 'Nissan', 'Kicks', 36000, ,
('Nissan Frontier X-Gear 2023', 'Pick-up doble cabina preparada para trabajo y ruta.', 31200000, 1, 'auto', 'Nissan', 'Frontier', 28000, 
('Honda Civic EX 2019', 'Sedán con buen nivel de confort y terminaciones.', 18800000, 1, 'auto', 'Honda', 'Civic', 67000, 
('Honda HR-V EXL 2022', 'SUV compacta espaciosa y confortable.', 22600000, 1, 'auto', 'Honda', 'HR-V', 35000, 
('Jeep Renegade Longitude 2022', 'SUV compacta para ciudad y escapadas.', 21400000, 1, 'auto', 'Jeep', 'Renegade', 33000, 
('Jeep Compass Limited 2023', 'SUV mediana con buen equipamiento.', 27900000, 1, 'auto', 'Jeep', 'Compass', 29000, 
('Hyundai Creta Safety 2022', 'SUV compacta con buen espacio y equipamiento.', 21200000, 1, 'auto', 'Hyundai', 'Creta', 40000, 
('Kia Rio EX 2019', 'Hatchback compacto y práctico.', 10800000, 1, 'auto', 'Kia', 'Rio', 76000, 
-- REPUESTOS ORIGINALES
('Pastillas de freno delanteras', 'Juego de pastillas para freno delantero.', 45000, 15, 'repuesto', 'Multimarca', 'Universal', 0, NULL),
('Batería 12V 75Ah', 'Batería para vehículos medianos y grandes.', 180000, 10, 'repuesto', 'Multimarca', 'Universal', 0, NULL),
('Filtro de aceite', 'Filtro de aceite para motor.', 12000, 30, 'repuesto', 'Multimarca', 'Universal', 0, NULL),
('Juego de bujías', 'Juego de bujías para motor naftero.', 35000, 20, 'repuesto', 'Multimarca', 'Universal', 0, NULL),
('Filtro de aire', 'Filtro de aire para mantenimiento preventivo.', 22000, 18, 'repuesto', 'Multimarca', 'Universal', 0, NULL),
('Correa de distribución', 'Kit de correa para distintos motores.', 95000, 8, 'repuesto', 'Multimarca', 'Universal', 0, NULL),

-- 30 REPUESTOS POR MARCA Y MODELO
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
