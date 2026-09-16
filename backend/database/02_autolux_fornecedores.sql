-- =============================================================================
-- Base de Dados 2 — AUTO LUX FORNECEDORES
-- Usada APENAS pelo Node.js: fornecedores e encomendas a fornecedores.
-- O PHP NÃO liga a esta base: fala com a API REST.
-- =============================================================================

CREATE DATABASE IF NOT EXISTS autolux_fornecedores
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE autolux_fornecedores;

CREATE USER IF NOT EXISTS 'autolux'@'%' IDENTIFIED BY 'autolux';
CREATE USER IF NOT EXISTS 'autolux'@'localhost' IDENTIFIED BY 'autolux';
GRANT ALL PRIVILEGES ON autolux_fornecedores.* TO 'autolux'@'%';
GRANT ALL PRIVILEGES ON autolux_fornecedores.* TO 'autolux'@'localhost';
FLUSH PRIVILEGES;

DROP TABLE IF EXISTS encomenda_itens;
DROP TABLE IF EXISTS encomendas;
DROP TABLE IF EXISTS fornecedores;

CREATE TABLE fornecedores (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(160) NOT NULL,
  email VARCHAR(160) NOT NULL,
  telefone VARCHAR(30) NOT NULL,
  nif VARCHAR(20) NOT NULL,
  morada VARCHAR(255) NOT NULL,
  especialidade VARCHAR(120) NOT NULL,
  prazo_entrega_dias INT NOT NULL DEFAULT 5,
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

CREATE TABLE encomendas (
  id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fornecedor_id INT UNSIGNED NOT NULL,
  peca_referencia VARCHAR(40) NOT NULL,
  peca_nome VARCHAR(160) NOT NULL,
  quantidade INT NOT NULL,
  preco_previsto DECIMAL(10,2) NOT NULL,
  observacoes TEXT NULL,
  estado VARCHAR(30) NOT NULL DEFAULT 'pendente',
  criado_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  CONSTRAINT fk_enc_fornecedor FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id)
) ENGINE=InnoDB;

INSERT INTO fornecedores (nome, email, telefone, nif, morada, especialidade, prazo_entrega_dias) VALUES
('Bosch Automotive Portugal', 'encomendas@bosch-auto.pt', '214123456', '500123456', 'Parque Industrial da Abrunheira, Sintra', 'Filtros, ignição e injeção', 4),
('Brembo Ibérica', 'orders@brembo.es', '+34 938 778 100', 'ESA08887766', 'Polígono Industrial, Barcelona', 'Sistemas de travagem', 7),
('Valeo Service Iberia', 'pedidos@valeo.pt', '219001122', '508776655', 'Zona Industrial da Abrunheira, Sintra', 'Elétrica e climatização', 5),
('NGK Spark Plugs Europe', 'sales.pt@ngkntk.com', '220998877', 'DE129988776', 'Armazém Norte, Vila Nova de Gaia', 'Velas e sondas lambda', 6),
('Recambios Castrol Lubrificantes', 'comercial@castrol-pt.pt', '218776655', '501445566', 'Rua do Petróleo 9, Matosinhos', 'Óleos e lubrificantes', 3);

INSERT INTO encomendas (fornecedor_id, peca_referencia, peca_nome, quantidade, preco_previsto, observacoes, estado) VALUES
(1, 'BOS-FO-001', 'Filtro de óleo P3330', 50, 6.20, 'Reposição de stock da semana.', 'recebida'),
(2, 'BRE-PT-320', 'Pastilhas de travão dianteiras', 20, 48.00, 'Pedido urgente — stock baixo.', 'pendente');
