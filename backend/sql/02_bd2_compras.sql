-- ============================================================
-- AutoLux - Base de Dados 2 (Compras)
-- Fornecedores e Encomendas a Fornecedores.
-- Acedida exclusivamente pelo serviço Node.js (mysql2).
-- ============================================================

DROP DATABASE IF EXISTS autolux_compras;
CREATE DATABASE autolux_compras CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE autolux_compras;

CREATE TABLE fornecedores (
    id                   INT AUTO_INCREMENT PRIMARY KEY,
    nome                 VARCHAR(120) NOT NULL,
    nif                  VARCHAR(20)  NOT NULL UNIQUE,
    email                VARCHAR(120) NOT NULL,
    telefone             VARCHAR(25)  NULL,
    pais                 VARCHAR(40)  NOT NULL DEFAULT 'Portugal',
    prazo_entrega_dias   INT          NOT NULL DEFAULT 5,
    ativo                TINYINT(1)   NOT NULL DEFAULT 1,
    criado_em            DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_fornecedores_nome (nome)
) ENGINE = InnoDB;

-- Catálogo que cada fornecedor disponibiliza ao armazém
CREATE TABLE fornecedor_artigos (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    fornecedor_id   INT            NOT NULL,
    referencia      VARCHAR(30)    NOT NULL,
    designacao      VARCHAR(120)   NOT NULL,
    preco_custo     DECIMAL(10, 2) NOT NULL,
    CONSTRAINT fk_artigos_fornecedor FOREIGN KEY (fornecedor_id) REFERENCES fornecedores (id) ON DELETE CASCADE,
    CONSTRAINT uq_artigo_fornecedor UNIQUE (fornecedor_id, referencia),
    CONSTRAINT ck_artigos_preco CHECK (preco_custo >= 0)
) ENGINE = InnoDB;

CREATE TABLE encomendas (
    id             INT AUTO_INCREMENT PRIMARY KEY,
    numero         VARCHAR(20)    NOT NULL UNIQUE,
    fornecedor_id  INT            NOT NULL,
    estado         ENUM('submetida', 'confirmada', 'recebida', 'cancelada') NOT NULL DEFAULT 'submetida',
    total          DECIMAL(10, 2) NOT NULL DEFAULT 0,
    data_prevista  DATE           NULL,
    observacoes    VARCHAR(255)   NULL,
    criada_em      DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    atualizada_em  DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    CONSTRAINT fk_encomendas_fornecedor FOREIGN KEY (fornecedor_id) REFERENCES fornecedores (id),
    INDEX idx_encomendas_fornecedor (fornecedor_id),
    INDEX idx_encomendas_estado (estado)
) ENGINE = InnoDB;

CREATE TABLE encomenda_itens (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    encomenda_id    INT            NOT NULL,
    referencia      VARCHAR(30)    NOT NULL,
    designacao      VARCHAR(120)   NOT NULL,
    quantidade      INT            NOT NULL,
    preco_unitario  DECIMAL(10, 2) NOT NULL,
    subtotal        DECIMAL(10, 2) NOT NULL,
    CONSTRAINT fk_enc_itens_encomenda FOREIGN KEY (encomenda_id) REFERENCES encomendas (id) ON DELETE CASCADE,
    CONSTRAINT ck_enc_itens_quantidade CHECK (quantidade > 0),
    INDEX idx_enc_itens_encomenda (encomenda_id)
) ENGINE = InnoDB;

-- ============================================================
-- Dados de exemplo
-- ============================================================
INSERT INTO fornecedores (nome, nif, email, telefone, pais, prazo_entrega_dias, ativo) VALUES
    ('Bosch Automotive Portugal',  '500100200', 'encomendas@bosch-pt.example',   '214 000 100', 'Portugal', 3, 1),
    ('Valeo Service Ibérica',      'ES98765432','pedidos@valeo-iberica.example', '+34 913 000 200', 'Espanha', 6, 1),
    ('Brembo Distribuição',        'IT12345678','ordini@brembo-dist.example',    '+39 035 000 300', 'Itália',  8, 1),
    ('Sachs & Partners',           'DE87654321','bestellung@sachs-p.example',    '+49 69 000 400',  'Alemanha',7, 1),
    ('NGK Ibérica',                'ES11223344','ventas@ngk-iberica.example',    '+34 932 000 500', 'Espanha', 5, 1),
    ('Mann-Filter Europa',         'DE55667788','orders@mannfilter-eu.example',  '+49 711 000 600', 'Alemanha',6, 1),
    ('Peças Atlântico, Lda.',      '509988776', 'geral@pecasatlantico.example',  '229 000 700', 'Portugal', 2, 1),
    ('Auto Import Sul',            '507766554', 'compras@autoimportsul.example', '289 000 800', 'Portugal', 4, 0);

INSERT INTO fornecedor_artigos (fornecedor_id, referencia, designacao, preco_custo) VALUES
    (1, 'BR-1004', 'Líquido de travões DOT 4 (1L)',         6.10),
    (1, 'FL-2003', 'Filtro de habitáculo com carvão ativo',13.20),
    (1, 'MO-4001', 'Kit de correia de distribuição',       112.40),
    (1, 'MO-4004', 'Turbocompressor recondicionado',       432.00),
    (1, 'IG-5002', 'Bobine de ignição',                     38.90),
    (1, 'AR-8002', 'Termostato com carcaça',                20.50),
    (2, 'EL-6001', 'Bateria 70Ah 640A',                     74.30),
    (2, 'EL-6002', 'Alternador 120A',                      158.60),
    (2, 'EL-6003', 'Motor de arranque',                    126.90),
    (2, 'AR-8001', 'Radiador de água',                      98.70),
    (2, 'AR-8003', 'Ventoinha do radiador',                 90.20),
    (3, 'BR-1001', 'Pastilhas de travão dianteiras',        39.50),
    (3, 'BR-1002', 'Discos de travão ventilados 320mm',     88.90),
    (4, 'SU-3002', 'Kit de molas desportivas',             141.00),
    (4, 'MO-4002', 'Bomba de água',                         46.15),
    (4, 'TR-7001', 'Kit de embraiagem',                    204.00),
    (4, 'TR-7002', 'Volante bimassa',                      289.50),
    (5, 'IG-5001', 'Velas de ignição irídio (jogo 4)',      34.20),
    (5, 'IG-5003', 'Velas de incandescência (jogo 4)',      29.80),
    (6, 'FL-2001', 'Filtro de óleo',                         5.40),
    (6, 'FL-2002', 'Filtro de ar motor',                    10.90),
    (6, 'FL-2004', 'Filtro de combustível diesel',          16.75),
    (6, 'AR-8004', 'Anticongelante G12+ (5L)',              18.30),
    (7, 'BR-1003', 'Kit de travão de mão',                  28.40),
    (7, 'SU-3001', 'Amortecedor traseiro a gás',            52.60),
    (7, 'SU-3003', 'Rolamento de roda dianteiro',           31.90),
    (7, 'SU-3004', 'Braço oscilante inferior',              61.25),
    (7, 'MO-4003', 'Junta de cabeça do motor',              24.80);

INSERT INTO encomendas (numero, fornecedor_id, estado, total, data_prevista, observacoes, criada_em) VALUES
    ('E2026-0001', 1, 'recebida',   1097.00, '2026-08-20', 'Reposição de stock de verão.',  '2026-08-14 11:05:00'),
    ('E2026-0002', 3, 'confirmada',  968.00, '2026-09-18', NULL,                            '2026-09-08 16:30:00'),
    ('E2026-0003', 6, 'submetida',   544.50, '2026-09-22', 'Entregar na doca 2.',           '2026-09-15 08:15:00');

INSERT INTO encomenda_itens (encomenda_id, referencia, designacao, quantidade, preco_unitario, subtotal) VALUES
    (1, 'MO-4001', 'Kit de correia de distribuição',        5, 112.40, 562.00),
    (1, 'IG-5002', 'Bobine de ignição',                    10,  38.90, 389.00),
    (1, 'AR-8002', 'Termostato com carcaça',                7,  20.50, 143.50),
    (2, 'BR-1001', 'Pastilhas de travão dianteiras',       12,  39.50, 474.00),
    (2, 'BR-1002', 'Discos de travão ventilados 320mm',     6,  88.90, 533.40),
    (3, 'FL-2001', 'Filtro de óleo',                       40,   5.40, 216.00),
    (3, 'FL-2002', 'Filtro de ar motor',                   15,  10.90, 163.50),
    (3, 'AR-8004', 'Anticongelante G12+ (5L)',              9,  18.30, 164.70);

UPDATE encomendas e
SET e.total = (SELECT COALESCE(SUM(i.subtotal), 0) FROM encomenda_itens i WHERE i.encomenda_id = e.id);
