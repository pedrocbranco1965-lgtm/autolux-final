-- =====================================================================
-- AutoLux - Base de Dados 2 (MySQL)
-- Fornecedores e Encomendas a Fornecedores
-- Acedida exclusivamente pela Web API em Node.js (node-api/)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS autolux_fornecedores
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE autolux_fornecedores;

DROP TABLE IF EXISTS encomendas_itens;
DROP TABLE IF EXISTS encomendas;
DROP TABLE IF EXISTS fornecedores;

-- ---------------------------------------------------------------------
-- Fornecedores
-- ---------------------------------------------------------------------
CREATE TABLE fornecedores (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome            VARCHAR(140) NOT NULL,
  nif             CHAR(9)      NOT NULL UNIQUE,
  email           VARCHAR(160) NOT NULL,
  telefone        VARCHAR(20)  NULL,
  morada          VARCHAR(200) NULL,
  prazo_entrega_dias INT       NOT NULL DEFAULT 5,
  ativo           TINYINT(1)   NOT NULL DEFAULT 1,
  criado_em       TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Encomendas a fornecedores (cabeçalho) e respetivas linhas
-- ---------------------------------------------------------------------
CREATE TABLE encomendas (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  fornecedor_id   INT UNSIGNED NOT NULL,
  data_encomenda  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  estado          ENUM('pendente','enviada','recebida','cancelada') NOT NULL DEFAULT 'pendente',
  total           DECIMAL(10,2) NOT NULL DEFAULT 0,
  observacoes     VARCHAR(255) NULL,
  criado_por      VARCHAR(120) NULL,   -- nome do funcionário que submeteu (enviado pela interface PHP)
  CONSTRAINT fk_enc_fornecedor FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id)
) ENGINE=InnoDB;

CREATE TABLE encomendas_itens (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  encomenda_id    INT UNSIGNED  NOT NULL,
  referencia_peca VARCHAR(40)   NOT NULL,
  descricao       VARCHAR(160)  NOT NULL,
  quantidade      INT           NOT NULL,
  preco_unitario  DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_enc_itens_encomenda FOREIGN KEY (encomenda_id) REFERENCES encomendas(id) ON DELETE CASCADE,
  CONSTRAINT chk_enc_itens_qtd CHECK (quantidade > 0)
) ENGINE=InnoDB;

CREATE INDEX idx_encomendas_fornecedor ON encomendas(fornecedor_id);
CREATE INDEX idx_encomendas_estado     ON encomendas(estado);

-- =====================================================================
-- Dados de exemplo
-- =====================================================================
INSERT INTO fornecedores (nome, nif, email, telefone, morada, prazo_entrega_dias) VALUES
  ('Bosch Portugal, S.A.',          '500123456', 'encomendas@bosch.pt',      '218500300', 'Av. Infante D. Henrique, Lisboa', 3),
  ('Brembo Ibérica',                '500234567', 'orders@brembo.es',         '+34915550101', 'Calle Mayor 10, Madrid', 7),
  ('Mann+Hummel Distribuição',      '500345678', 'pt.orders@mann-hummel.com','229000111', 'Rua da Indústria 45, Maia', 4),
  ('Valeo Service Portugal',        '500456789', 'service.pt@valeo.com',     '214200200', 'Parque Industrial, Sintra', 5),
  ('AutoDistribuição Nacional Lda', '500567890', 'compras@autodist.pt',      '244800800', 'Zona Industrial Norte, Leiria', 2);

INSERT INTO encomendas (fornecedor_id, estado, total, observacoes, criado_por)
VALUES (1, 'recebida', 858.00, 'Reposição mensal de pastilhas', 'Ana Martins');

INSERT INTO encomendas_itens (encomenda_id, referencia_peca, descricao, quantidade, preco_unitario) VALUES
  (1, 'BR-0986494', 'Pastilhas de travão dianteiras', 20, 28.60),
  (1, 'FL-F026400', 'Filtro de habitáculo carvão ativo', 25, 11.44);
