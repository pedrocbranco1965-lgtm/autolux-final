-- =====================================================================
-- AutoLux - Base de Dados 1 (MySQL)
-- Clientes, Material/Stock (peças) e Vendas
-- Usada pela camada PHP (Apresentação e Vendas)
-- =====================================================================

CREATE DATABASE IF NOT EXISTS autolux_vendas
  CHARACTER SET utf8mb4
  COLLATE utf8mb4_unicode_ci;

USE autolux_vendas;

DROP TABLE IF EXISTS vendas_itens;
DROP TABLE IF EXISTS vendas;
DROP TABLE IF EXISTS pecas;
DROP TABLE IF EXISTS tipos_peca;
DROP TABLE IF EXISTS marcas;
DROP TABLE IF EXISTS clientes;

-- ---------------------------------------------------------------------
-- Clientes
-- ---------------------------------------------------------------------
CREATE TABLE clientes (
  id          INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome        VARCHAR(120)  NOT NULL,
  nif         CHAR(9)       NOT NULL UNIQUE,
  email       VARCHAR(160)  NOT NULL,
  telefone    VARCHAR(20)   NULL,
  morada      VARCHAR(200)  NULL,
  criado_em   TIMESTAMP     NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Tabelas de apoio ao catálogo: marcas e tipos de peça
-- ---------------------------------------------------------------------
CREATE TABLE marcas (
  id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome  VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

CREATE TABLE tipos_peca (
  id    INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome  VARCHAR(80) NOT NULL UNIQUE
) ENGINE=InnoDB;

-- ---------------------------------------------------------------------
-- Peças (material / stock)
-- ---------------------------------------------------------------------
CREATE TABLE pecas (
  id            INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  referencia    VARCHAR(40)   NOT NULL UNIQUE,
  nome          VARCHAR(140)  NOT NULL,
  descricao     TEXT          NULL,
  marca_id      INT UNSIGNED  NOT NULL,
  tipo_id       INT UNSIGNED  NOT NULL,
  preco         DECIMAL(10,2) NOT NULL,
  stock         INT           NOT NULL DEFAULT 0,
  stock_minimo  INT           NOT NULL DEFAULT 5,
  CONSTRAINT fk_pecas_marca FOREIGN KEY (marca_id) REFERENCES marcas(id),
  CONSTRAINT fk_pecas_tipo  FOREIGN KEY (tipo_id)  REFERENCES tipos_peca(id),
  CONSTRAINT chk_pecas_preco CHECK (preco >= 0),
  CONSTRAINT chk_pecas_stock CHECK (stock >= 0)
) ENGINE=InnoDB;

CREATE INDEX idx_pecas_marca ON pecas(marca_id);
CREATE INDEX idx_pecas_tipo  ON pecas(tipo_id);
CREATE INDEX idx_pecas_preco ON pecas(preco);

-- ---------------------------------------------------------------------
-- Vendas (cabeçalho) e itens da venda
-- O tipo de pagamento é guardado como código (string) porque a lista de
-- métodos de pagamento vive no PHP (php/config/pagamentos.php) para ser
-- fácil de estender sem alterar a base de dados.
-- ---------------------------------------------------------------------
CREATE TABLE vendas (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  cliente_id      INT UNSIGNED  NOT NULL,
  data_venda      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tipo_pagamento  VARCHAR(40)   NOT NULL,
  detalhe_pagamento VARCHAR(160) NULL,
  total           DECIMAL(10,2) NOT NULL DEFAULT 0,
  observacoes     VARCHAR(255)  NULL,
  CONSTRAINT fk_vendas_cliente FOREIGN KEY (cliente_id) REFERENCES clientes(id)
) ENGINE=InnoDB;

CREATE TABLE vendas_itens (
  id              INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  venda_id        INT UNSIGNED  NOT NULL,
  peca_id         INT UNSIGNED  NOT NULL,
  quantidade      INT           NOT NULL,
  preco_unitario  DECIMAL(10,2) NOT NULL,
  CONSTRAINT fk_itens_venda FOREIGN KEY (venda_id) REFERENCES vendas(id) ON DELETE CASCADE,
  CONSTRAINT fk_itens_peca  FOREIGN KEY (peca_id)  REFERENCES pecas(id),
  CONSTRAINT chk_itens_qtd CHECK (quantidade > 0)
) ENGINE=InnoDB;

-- =====================================================================
-- Dados de exemplo
-- =====================================================================
INSERT INTO clientes (nome, nif, email, telefone, morada) VALUES
  ('Oficina Silva & Filhos',   '501234567', 'geral@oficinasilva.pt',   '212345678', 'Rua das Oficinas 12, Setúbal'),
  ('Auto Reparações Norte',    '502345678', 'compras@arnorte.pt',      '225678901', 'Av. da Boavista 900, Porto'),
  ('João Pereira',             '223456789', 'joao.pereira@gmail.com',  '912345678', 'Rua Nova 4, Coimbra'),
  ('Mecânica Rápida Lda',      '503456789', 'encomendas@mecrapida.pt', '239876543', 'Zona Industrial Lote 7, Leiria'),
  ('Maria Fernandes',          '234567890', 'maria.f@hotmail.com',     '934567890', 'Praceta do Sol 3, Faro');

INSERT INTO marcas (nome) VALUES
  ('Bosch'), ('Brembo'), ('Valeo'), ('Mann-Filter'), ('NGK'), ('Castrol'), ('Continental'), ('SKF');

INSERT INTO tipos_peca (nome) VALUES
  ('Travões'), ('Filtros'), ('Ignição'), ('Suspensão'), ('Óleos e Lubrificantes'), ('Elétrica'), ('Correias'), ('Rolamentos');

INSERT INTO pecas (referencia, nome, descricao, marca_id, tipo_id, preco, stock, stock_minimo) VALUES
  ('BR-0986494', 'Pastilhas de travão dianteiras', 'Jogo de pastilhas cerâmicas, baixa emissão de pó.',
    (SELECT id FROM marcas WHERE nome='Bosch'),        (SELECT id FROM tipos_peca WHERE nome='Travões'), 42.90, 35, 10),
  ('BR-09A6641', 'Disco de travão ventilado 300mm', 'Disco ventilado de alta performance, par.',
    (SELECT id FROM marcas WHERE nome='Brembo'),       (SELECT id FROM tipos_peca WHERE nome='Travões'), 118.50, 12, 6),
  ('FL-W71230',  'Filtro de óleo',                   'Filtro de óleo de fluxo total com válvula anti-retorno.',
    (SELECT id FROM marcas WHERE nome='Mann-Filter'),  (SELECT id FROM tipos_peca WHERE nome='Filtros'), 9.75, 120, 30),
  ('FL-C30135',  'Filtro de ar',                     'Filtro de ar de painel para motores 1.6/2.0.',
    (SELECT id FROM marcas WHERE nome='Mann-Filter'),  (SELECT id FROM tipos_peca WHERE nome='Filtros'), 14.20, 80, 20),
  ('FL-F026400', 'Filtro de habitáculo carvão ativo','Filtra pólen e odores.',
    (SELECT id FROM marcas WHERE nome='Bosch'),        (SELECT id FROM tipos_peca WHERE nome='Filtros'), 17.60, 4, 10),
  ('IG-BKR6E',   'Vela de ignição BKR6E',            'Vela de ignição standard, unidade.',
    (SELECT id FROM marcas WHERE nome='NGK'),          (SELECT id FROM tipos_peca WHERE nome='Ignição'), 4.30, 200, 50),
  ('IG-0221504', 'Bobina de ignição',                'Bobina de ignição individual tipo caneta.',
    (SELECT id FROM marcas WHERE nome='Bosch'),        (SELECT id FROM tipos_peca WHERE nome='Ignição'), 38.00, 25, 8),
  ('SU-VKDS32',  'Kit de rolamento de roda dianteiro','Rolamento com sensor ABS integrado.',
    (SELECT id FROM marcas WHERE nome='SKF'),          (SELECT id FROM tipos_peca WHERE nome='Rolamentos'), 64.90, 9, 5),
  ('OL-EDGE5W30','Óleo Castrol Edge 5W-30 5L',       'Óleo totalmente sintético, especificação LL-04.',
    (SELECT id FROM marcas WHERE nome='Castrol'),      (SELECT id FROM tipos_peca WHERE nome='Óleos e Lubrificantes'), 46.99, 40, 15),
  ('CO-6PK1200', 'Correia poli-V 6PK1200',           'Correia de acessórios de 6 nervuras.',
    (SELECT id FROM marcas WHERE nome='Continental'),  (SELECT id FROM tipos_peca WHERE nome='Correias'), 21.40, 18, 6),
  ('CO-CT1028K', 'Kit de distribuição com bomba de água','Kit completo com tensor e bomba de água.',
    (SELECT id FROM marcas WHERE nome='Continental'),  (SELECT id FROM tipos_peca WHERE nome='Correias'), 189.00, 6, 3),
  ('EL-437010',  'Alternador 120A',                  'Alternador remanufaturado 12V 120A.',
    (SELECT id FROM marcas WHERE nome='Valeo'),        (SELECT id FROM tipos_peca WHERE nome='Elétrica'), 245.00, 3, 2),
  ('EL-458170',  'Motor de arranque',                'Motor de arranque 1.4kW.',
    (SELECT id FROM marcas WHERE nome='Valeo'),        (SELECT id FROM tipos_peca WHERE nome='Elétrica'), 168.30, 5, 2),
  ('SU-3340701', 'Amortecedor traseiro a gás',       'Amortecedor bitubo a gás, unidade.',
    (SELECT id FROM marcas WHERE nome='Bosch'),        (SELECT id FROM tipos_peca WHERE nome='Suspensão'), 57.80, 22, 8),
  ('BR-P85020',  'Pastilhas de travão traseiras',    'Pastilhas traseiras com indicador de desgaste.',
    (SELECT id FROM marcas WHERE nome='Brembo'),       (SELECT id FROM tipos_peca WHERE nome='Travões'), 36.40, 28, 10);

-- Uma venda de exemplo já registada
INSERT INTO vendas (cliente_id, tipo_pagamento, detalhe_pagamento, total, observacoes)
VALUES (1, 'multibanco', 'Ref. 123 456 789', 95.55, 'Venda de demonstração');

INSERT INTO vendas_itens (venda_id, peca_id, quantidade, preco_unitario) VALUES
  (1, (SELECT id FROM pecas WHERE referencia='BR-0986494'), 2, 42.90),
  (1, (SELECT id FROM pecas WHERE referencia='FL-W71230'),  1, 9.75);
