-- =============================================================================
-- Base de Dados 1 — AUTO LUX VENDAS
-- Usada pelo PHP: clientes, material/stock e vendas.
-- =============================================================================

CREATE DATABASE IF NOT EXISTS autolux_vendas
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE autolux_vendas;

CREATE USER IF NOT EXISTS 'autolux'@'%' IDENTIFIED BY 'autolux';
CREATE USER IF NOT EXISTS 'autolux'@'localhost' IDENTIFIED BY 'autolux';
GRANT ALL PRIVILEGES ON autolux_vendas.* TO 'autolux'@'%';
GRANT ALL PRIVILEGES ON autolux_vendas.* TO 'autolux'@'localhost';
FLUSH PRIVILEGES;

DROP TABLE IF EXISTS vendas;
DROP TABLE IF EXISTS pecas;
DROP TABLE IF EXISTS clientes;
DROP TABLE IF EXISTS funcionarios;

CREATE TABLE funcionarios (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  username VARCHAR(60) NOT NULL UNIQUE,
  password_hash VARCHAR(255) NOT NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE clientes (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(160) NOT NULL,
  email VARCHAR(160) NOT NULL,
  telefone VARCHAR(30) NOT NULL,
  nif VARCHAR(20) NOT NULL,
  morada VARCHAR(255) NOT NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE pecas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  referencia VARCHAR(40) NOT NULL UNIQUE,
  nome VARCHAR(160) NOT NULL,
  marca VARCHAR(80) NOT NULL,
  tipo VARCHAR(80) NOT NULL,
  preco DECIMAL(10,2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  descricao TEXT NOT NULL,
  imagem VARCHAR(500) NOT NULL,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE vendas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT UNSIGNED NOT NULL,
  peca_id INT UNSIGNED NOT NULL,
  quantidade INT NOT NULL,
  preco_unitario DECIMAL(10,2) NOT NULL,
  total DECIMAL(10,2) NOT NULL,
  tipo_pagamento VARCHAR(40) NOT NULL,
  notas_pagamento TEXT NULL,
  estado VARCHAR(30) NOT NULL DEFAULT 'registada',
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_venda_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id),
  CONSTRAINT fk_venda_peca FOREIGN KEY (peca_id) REFERENCES pecas(id)
) ENGINE=InnoDB;

-- Palavra-passe de ambos: autolux
INSERT INTO funcionarios (nome, username, password_hash) VALUES
('Administrador AutoLux', 'admin', '$2y$10$DoS1sHRAOSoovvZUoo0NueGeYhP2hp8dNeI4gmkd32nf9ropv2py6'),
('Funcionário Balcão', 'funcionario', '$2y$10$DoS1sHRAOSoovvZUoo0NueGeYhP2hp8dNeI4gmkd32nf9ropv2py6');

INSERT INTO clientes (nome, email, telefone, nif, morada) VALUES
('Oficina Silva & Filhos', 'oficina.silva@email.pt', '219876543', '509123456', 'Rua da Indústria 12, 2685-000 Loures'),
('Auto Sport Braga', 'contacto@autosportbraga.pt', '253112233', '507654321', 'Avenida da Liberdade 88, 4700-000 Braga'),
('Maria João Ferreira', 'mjferreira@email.pt', '912345678', '198765432', 'Rua das Flores 4, 4000-000 Porto'),
('Garagem Central Almada', 'geral@garagemcentral.pt', '212334455', '510998877', 'Rua Cândido dos Reis 21, 2800-000 Almada'),
('Pedro Nunes Mecânica', 'pedro.nunes@email.pt', '934567890', '203456789', 'Estrada Nacional 10, 2950-000 Palmela'),
('Inês Costa', 'ines.costa@email.pt', '967112244', '221334455', 'Avenida 25 de Abril 15, 8000-000 Faro');

INSERT INTO pecas (referencia, nome, marca, tipo, preco, stock, descricao, imagem) VALUES
('BOS-FO-001', 'Filtro de óleo P3330', 'Bosch', 'Filtro', 8.90, 42, 'Filtro de óleo original Bosch para motores a gasolina e diesel. Substituição recomendada a cada 15 000 km.', 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=800'),
('MAN-FA-014', 'Filtro de ar C 27 127', 'Mann-Filter', 'Filtro', 16.50, 28, 'Filtro de ar de elevado caudal, reduz o consumo e protege o motor de partículas.', 'https://images.unsplash.com/photo-1487754180451-c456f719a1fc?w=800'),
('BRE-PT-320', 'Pastilhas de travão dianteiras', 'Brembo', 'Travagem', 64.90, 18, 'Jogo de pastilhas cerâmicas Brembo para eixo dianteiro. Baixo ruído e menor desgaste do disco.', 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=800'),
('BRE-DT-118', 'Disco de travão ventilado 280mm', 'Brembo', 'Travagem', 49.00, 12, 'Disco ventilado de 280 mm, tratamento anticorrosão. Vendido à unidade.', 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800'),
('NGK-VI-095', 'Vela de ignição Iridium IX', 'NGK', 'Ignição', 12.75, 60, 'Vela iridium de longa duração. Melhora a arranque a frio e a combustão.', 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800'),
('VAR-BT-074', 'Bateria Silver Dynamic 74Ah', 'Varta', 'Elétrica', 119.00, 7, 'Bateria 12V 74Ah 680A. Adequada a veículos com Start-Stop básico.', 'https://images.unsplash.com/photo-1619642751034-76571cca2583?w=800'),
('SAC-AM-210', 'Amortecedor dianteiro Super Touring', 'Sachs', 'Suspensão', 89.50, 9, 'Amortecedor de pressão de gás para eixo dianteiro. Conforto e estabilidade em estrada.', 'https://images.unsplash.com/photo-1493238792000-8113da705763?w=800'),
('GAT-CD-055', 'Kit correia de distribuição', 'Gates', 'Motor', 142.00, 6, 'Kit completo: correia, tensor e bomba de água. Substituição aos 90 000 / 120 000 km conforme marca.', 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=800'),
('CAS-OL-5W30', 'Óleo de motor Magnatec 5W-30 5L', 'Castrol', 'Lubrificante', 38.90, 35, 'Óleo sintético 5W-30 ACEA C3. Proteção em arranques a frio e intervalos alargados.', 'https://images.unsplash.com/photo-1487754180451-c456f719a1fc?w=800'),
('SKF-RO-440', 'Rolamento de roda kit VKBA 3644', 'SKF', 'Transmissão', 54.20, 14, 'Kit de rolamento de roda com cubo. Inclui anel ABS onde aplicável.', 'https://images.unsplash.com/photo-1517048676732-d65bc937f952?w=800'),
('OSR-LP-H7', 'Lâmpada H7 Night Breaker 2un', 'Osram', 'Iluminação', 22.40, 40, 'Par de lâmpadas H7 com até +150% de luminosidade. Homologadas para estrada.', 'https://images.unsplash.com/photo-1503376780353-7e6692767b70?w=800'),
('LUK-EM-620', 'Kit de embraiagem RepSet', 'LuK', 'Transmissão', 189.00, 4, 'Kit com disco, placa de pressão e rolamento de encosto. Para veículos utilitários a gasolina.', 'https://images.unsplash.com/photo-1486262715619-67b85e0b08d3?w=800'),
('VAL-AL-330', 'Alternador 120A reconstruído', 'Valeo', 'Elétrica', 165.00, 3, 'Alternador Valeo 120A, reconstruído com garantia de 24 meses. Testado em banco.', 'https://images.unsplash.com/photo-1619642751034-76571cca2583?w=800'),
('FEB-SN-ABS', 'Sensor ABS dianteiro direito', 'Febi', 'Elétrica', 27.80, 16, 'Sensor de velocidade da roda, conector original. Compatível com várias plataformas PSA e VAG.', 'https://images.unsplash.com/photo-1492144534655-ae79c964c9d7?w=800'),
('BOS-IN-401', 'Injetador diesel common-rail', 'Bosch', 'Motor', 210.00, 5, 'Injetador Bosch reconstruído, debitado e selado. Exige código de programação na viatura.', 'https://images.unsplash.com/photo-1487754180451-c456f719a1fc?w=800');
