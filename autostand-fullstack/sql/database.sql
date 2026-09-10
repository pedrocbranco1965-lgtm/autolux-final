CREATE DATABASE IF NOT EXISTS autostand_db
CHARACTER SET utf8mb4
COLLATE utf8mb4_unicode_ci;

USE autostand_db;

CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(150) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'viewer') NOT NULL DEFAULT 'viewer',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS veiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('carro', 'moto') NOT NULL,
    marca VARCHAR(80) NOT NULL,
    modelo VARCHAR(100) NOT NULL,
    ano SMALLINT UNSIGNED NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    combustivel ENUM('gasolina', 'diesel', 'hibrido', 'eletrico', 'outro') NOT NULL,
    quilometragem INT UNSIGNED NOT NULL DEFAULT 0,
    portas TINYINT UNSIGNED NULL,
    cilindradas INT UNSIGNED NULL,
    estado ENUM('disponivel', 'reservado', 'vendido') NOT NULL DEFAULT 'disponivel',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

CREATE TABLE IF NOT EXISTS vendas (
    id INT AUTO_INCREMENT PRIMARY KEY,
    vehicle_id INT NOT NULL,
    customer_name VARCHAR(120) NOT NULL,
    customer_email VARCHAR(150) NOT NULL,
    sale_price DECIMAL(10,2) NOT NULL,
    user_id INT NOT NULL,
    sold_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_vendas_veiculo
        FOREIGN KEY (vehicle_id) REFERENCES veiculos(id)
        ON DELETE RESTRICT ON UPDATE CASCADE,
    CONSTRAINT fk_vendas_user
        FOREIGN KEY (user_id) REFERENCES users(id)
        ON DELETE RESTRICT ON UPDATE CASCADE
);

-- Dados de exemplo. Cada linha so e inserida se ainda nao existir a mesma marca/modelo.
INSERT INTO veiculos
(tipo, marca, modelo, ano, preco, combustivel, quilometragem, portas, cilindradas, estado)
SELECT 'carro', 'BMW', 'M3', 2024, 89900.00, 'gasolina', 12000, 4, NULL, 'disponivel'
WHERE NOT EXISTS (SELECT 1 FROM veiculos WHERE marca = 'BMW' AND modelo = 'M3');

INSERT INTO veiculos
(tipo, marca, modelo, ano, preco, combustivel, quilometragem, portas, cilindradas, estado)
SELECT 'carro', 'Toyota', 'Corolla', 2023, 27500.00, 'hibrido', 18500, 5, NULL, 'disponivel'
WHERE NOT EXISTS (SELECT 1 FROM veiculos WHERE marca = 'Toyota' AND modelo = 'Corolla');

INSERT INTO veiculos
(tipo, marca, modelo, ano, preco, combustivel, quilometragem, portas, cilindradas, estado)
SELECT 'carro', 'Tesla', 'Model 3', 2025, 44900.00, 'eletrico', 3200, 4, NULL, 'reservado'
WHERE NOT EXISTS (SELECT 1 FROM veiculos WHERE marca = 'Tesla' AND modelo = 'Model 3');

INSERT INTO veiculos
(tipo, marca, modelo, ano, preco, combustivel, quilometragem, portas, cilindradas, estado)
SELECT 'moto', 'Honda', 'CBR 600', 2022, 11800.00, 'gasolina', 9300, NULL, 600, 'disponivel'
WHERE NOT EXISTS (SELECT 1 FROM veiculos WHERE marca = 'Honda' AND modelo = 'CBR 600');

INSERT INTO veiculos
(tipo, marca, modelo, ano, preco, combustivel, quilometragem, portas, cilindradas, estado)
SELECT 'moto', 'Yamaha', 'MT-07', 2024, 8290.00, 'gasolina', 2100, NULL, 689, 'disponivel'
WHERE NOT EXISTS (SELECT 1 FROM veiculos WHERE marca = 'Yamaha' AND modelo = 'MT-07');

INSERT INTO veiculos
(tipo, marca, modelo, ano, preco, combustivel, quilometragem, portas, cilindradas, estado)
SELECT 'carro', 'Mercedes', 'C200', 2024, 52900.00, 'diesel', 8100, 4, NULL, 'disponivel'
WHERE NOT EXISTS (SELECT 1 FROM veiculos WHERE marca = 'Mercedes' AND modelo = 'C200');

INSERT INTO veiculos
(tipo, marca, modelo, ano, preco, combustivel, quilometragem, portas, cilindradas, estado)
SELECT 'carro', 'Volkswagen', 'Golf 8', 2023, 27500.00, 'gasolina', 15000, 5, NULL, 'disponivel'
WHERE NOT EXISTS (SELECT 1 FROM veiculos WHERE marca = 'Volkswagen' AND modelo = 'Golf 8');

INSERT INTO veiculos
(tipo, marca, modelo, ano, preco, combustivel, quilometragem, portas, cilindradas, estado)
SELECT 'carro', 'Peugeot', '3008', 2021, 26800.00, 'diesel', 62000, 5, NULL, 'disponivel'
WHERE NOT EXISTS (SELECT 1 FROM veiculos WHERE marca = 'Peugeot' AND modelo = '3008');

INSERT INTO veiculos
(tipo, marca, modelo, ano, preco, combustivel, quilometragem, portas, cilindradas, estado)
SELECT 'carro', 'Audi', 'A4', 2022, 34900.00, 'diesel', 41000, 4, NULL, 'disponivel'
WHERE NOT EXISTS (SELECT 1 FROM veiculos WHERE marca = 'Audi' AND modelo = 'A4');

INSERT INTO veiculos
(tipo, marca, modelo, ano, preco, combustivel, quilometragem, portas, cilindradas, estado)
SELECT 'carro', 'Renault', 'Clio', 2020, 14500.00, 'gasolina', 54000, 5, NULL, 'disponivel'
WHERE NOT EXISTS (SELECT 1 FROM veiculos WHERE marca = 'Renault' AND modelo = 'Clio');

INSERT INTO veiculos
(tipo, marca, modelo, ano, preco, combustivel, quilometragem, portas, cilindradas, estado)
SELECT 'moto', 'Ducati', 'Monster', 2024, 14900.00, 'gasolina', 850, NULL, 937, 'disponivel'
WHERE NOT EXISTS (SELECT 1 FROM veiculos WHERE marca = 'Ducati' AND modelo = 'Monster');

INSERT INTO veiculos
(tipo, marca, modelo, ano, preco, combustivel, quilometragem, portas, cilindradas, estado)
SELECT 'moto', 'Kawasaki', 'Z900', 2023, 10200.00, 'gasolina', 4100, NULL, 948, 'disponivel'
WHERE NOT EXISTS (SELECT 1 FROM veiculos WHERE marca = 'Kawasaki' AND modelo = 'Z900');

