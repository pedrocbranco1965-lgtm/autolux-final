-- ============================================================
-- AutoLux - Base de Dados 1 (Vendas)
-- Clientes, Material/Stock e Vendas.
-- Acedida diretamente pela camada PHP (PDO).
-- ============================================================

DROP DATABASE IF EXISTS autolux_vendas;
CREATE DATABASE autolux_vendas CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE autolux_vendas;

-- ------------------------------------------------------------
-- Tabelas de apoio ao catálogo
-- ------------------------------------------------------------
CREATE TABLE marcas (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    nome  VARCHAR(60) NOT NULL UNIQUE
) ENGINE = InnoDB;

CREATE TABLE tipos_peca (
    id    INT AUTO_INCREMENT PRIMARY KEY,
    nome  VARCHAR(60) NOT NULL UNIQUE
) ENGINE = InnoDB;

CREATE TABLE pecas (
    id            INT AUTO_INCREMENT PRIMARY KEY,
    referencia    VARCHAR(30)    NOT NULL UNIQUE,
    designacao    VARCHAR(120)   NOT NULL,
    marca_id      INT            NOT NULL,
    tipo_id       INT            NOT NULL,
    preco         DECIMAL(10, 2) NOT NULL,
    stock         INT            NOT NULL DEFAULT 0,
    stock_minimo  INT            NOT NULL DEFAULT 2,
    descricao     TEXT           NULL,
    ativo         TINYINT(1)     NOT NULL DEFAULT 1,
    criada_em     DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_pecas_marca FOREIGN KEY (marca_id) REFERENCES marcas (id),
    CONSTRAINT fk_pecas_tipo  FOREIGN KEY (tipo_id)  REFERENCES tipos_peca (id),
    CONSTRAINT ck_pecas_preco CHECK (preco >= 0),
    CONSTRAINT ck_pecas_stock CHECK (stock >= 0),
    INDEX idx_pecas_marca (marca_id),
    INDEX idx_pecas_tipo (tipo_id),
    INDEX idx_pecas_preco (preco)
) ENGINE = InnoDB;

-- ------------------------------------------------------------
-- Clientes
-- ------------------------------------------------------------
CREATE TABLE clientes (
    id         INT AUTO_INCREMENT PRIMARY KEY,
    nome       VARCHAR(120) NOT NULL,
    nif        VARCHAR(9)   NOT NULL UNIQUE,
    email      VARCHAR(120) NOT NULL,
    telefone   VARCHAR(20)  NULL,
    morada     VARCHAR(180) NULL,
    ativo      TINYINT(1)   NOT NULL DEFAULT 1,
    criado_em  DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_clientes_nome (nome)
) ENGINE = InnoDB;

-- ------------------------------------------------------------
-- Métodos de pagamento
--
-- Os métodos vivem em tabela para que acrescentar um novo tipo de
-- pagamento seja apenas um INSERT: a página PHP constrói o dropdown a
-- partir daqui e a classe correspondente (App\Payment\*) é resolvida pelo
-- código. Se não existir classe dedicada, é usado o método genérico.
-- ------------------------------------------------------------
CREATE TABLE metodos_pagamento (
    id          INT AUTO_INCREMENT PRIMARY KEY,
    codigo      VARCHAR(30)  NOT NULL UNIQUE,
    designacao  VARCHAR(60)  NOT NULL,
    descricao   VARCHAR(180) NULL,
    ativo       TINYINT(1)   NOT NULL DEFAULT 1,
    ordem       INT          NOT NULL DEFAULT 0
) ENGINE = InnoDB;

-- ------------------------------------------------------------
-- Vendas (encomendas de peças para cliente)
-- ------------------------------------------------------------
CREATE TABLE vendas (
    id                    INT AUTO_INCREMENT PRIMARY KEY,
    numero                VARCHAR(20)    NOT NULL UNIQUE,
    cliente_id            INT            NOT NULL,
    metodo_pagamento_id   INT            NOT NULL,
    referencia_pagamento  VARCHAR(60)    NULL,
    estado                ENUM('pendente', 'paga', 'anulada') NOT NULL DEFAULT 'pendente',
    total                 DECIMAL(10, 2) NOT NULL DEFAULT 0,
    observacoes           VARCHAR(255)   NULL,
    criada_em             DATETIME       NOT NULL DEFAULT CURRENT_TIMESTAMP,
    CONSTRAINT fk_vendas_cliente FOREIGN KEY (cliente_id)          REFERENCES clientes (id),
    CONSTRAINT fk_vendas_metodo  FOREIGN KEY (metodo_pagamento_id) REFERENCES metodos_pagamento (id),
    INDEX idx_vendas_cliente (cliente_id),
    INDEX idx_vendas_data (criada_em)
) ENGINE = InnoDB;

CREATE TABLE venda_itens (
    id              INT AUTO_INCREMENT PRIMARY KEY,
    venda_id        INT            NOT NULL,
    peca_id         INT            NOT NULL,
    quantidade      INT            NOT NULL,
    preco_unitario  DECIMAL(10, 2) NOT NULL,
    subtotal        DECIMAL(10, 2) NOT NULL,
    CONSTRAINT fk_itens_venda FOREIGN KEY (venda_id) REFERENCES vendas (id) ON DELETE CASCADE,
    CONSTRAINT fk_itens_peca  FOREIGN KEY (peca_id)  REFERENCES pecas (id),
    CONSTRAINT ck_itens_quantidade CHECK (quantidade > 0),
    INDEX idx_itens_venda (venda_id)
) ENGINE = InnoDB;

-- ------------------------------------------------------------
-- Vista usada pelo catálogo: evita repetir os JOIN em cada consulta
-- ------------------------------------------------------------
CREATE OR REPLACE VIEW vw_catalogo_pecas AS
SELECT p.id,
       p.referencia,
       p.designacao,
       p.preco,
       p.stock,
       p.stock_minimo,
       p.descricao,
       p.ativo,
       m.id   AS marca_id,
       m.nome AS marca,
       t.id   AS tipo_id,
       t.nome AS tipo
FROM pecas p
         INNER JOIN marcas m ON m.id = p.marca_id
         INNER JOIN tipos_peca t ON t.id = p.tipo_id;

-- ============================================================
-- Dados de exemplo
-- ============================================================
INSERT INTO marcas (nome) VALUES
    ('Bosch'), ('Valeo'), ('Brembo'), ('Sachs'), ('NGK'), ('Mann-Filter'), ('Monroe'), ('Febi Bilstein');

INSERT INTO tipos_peca (nome) VALUES
    ('Travagem'), ('Filtros'), ('Suspensão'), ('Motor'), ('Ignição'), ('Elétrica'), ('Transmissão'), ('Arrefecimento');

INSERT INTO pecas (referencia, designacao, marca_id, tipo_id, preco, stock, stock_minimo, descricao) VALUES
    ('BR-1001', 'Pastilhas de travão dianteiras',        3, 1,  64.90, 24, 6,  'Jogo de 4 pastilhas cerâmicas de baixo ruído.'),
    ('BR-1002', 'Discos de travão ventilados 320mm',     3, 1, 139.50, 12, 4,  'Par de discos ventilados com tratamento anticorrosão.'),
    ('BR-1003', 'Kit de travão de mão',                  8, 1,  48.20,  9, 3,  'Kit completo com molas e calços.'),
    ('BR-1004', 'Líquido de travões DOT 4 (1L)',         1, 1,  11.75, 60, 15, 'Ponto de ebulição seco 260 °C.'),
    ('FL-2001', 'Filtro de óleo',                        6, 2,   9.80, 85, 20, 'Compatível com motores 1.6 e 2.0 diesel.'),
    ('FL-2002', 'Filtro de ar motor',                    6, 2,  18.40, 47, 12, 'Elemento filtrante de alta eficiência.'),
    ('FL-2003', 'Filtro de habitáculo com carvão ativo', 1, 2,  22.30, 38, 10, 'Retém pólen, fuligem e maus odores.'),
    ('FL-2004', 'Filtro de combustível diesel',          6, 2,  27.60, 26, 8,  'Com separador de água integrado.'),
    ('SU-3001', 'Amortecedor traseiro a gás',            7, 3,  86.00, 18, 6,  'Vendido à unidade, tecnologia bitubo.'),
    ('SU-3002', 'Kit de molas desportivas',              4, 3, 214.90,  6, 2,  'Rebaixamento de 30 mm, homologado.'),
    ('SU-3003', 'Rolamento de roda dianteiro',           8, 3,  54.30, 21, 6,  'Kit com porca e anel de retenção.'),
    ('SU-3004', 'Braço oscilante inferior',              8, 3,  97.45, 10, 3,  'Com casquilhos e rótula montados.'),
    ('MO-4001', 'Kit de correia de distribuição',        1, 4, 179.00, 11, 4,  'Correia, tensor e roletes.'),
    ('MO-4002', 'Bomba de água',                         4, 4,  73.25, 14, 5,  'Corpo em alumínio com vedante reforçado.'),
    ('MO-4003', 'Junta de cabeça do motor',              8, 4,  41.10,  8, 3,  'Multicamada metálica (MLS).'),
    ('MO-4004', 'Turbocompressor recondicionado',        1, 4, 689.00,  3, 1,  'Recondicionado em fábrica, garantia 2 anos.'),
    ('IG-5001', 'Velas de ignição irídio (jogo 4)',      5, 5,  56.80, 40, 10, 'Maior durabilidade e arranque a frio.'),
    ('IG-5002', 'Bobine de ignição',                     1, 5,  62.15, 17, 5,  'Resistência ao calor até 150 °C.'),
    ('IG-5003', 'Velas de incandescência (jogo 4)',      1, 5,  48.90, 23, 6,  'Aquecimento rápido em 3 segundos.'),
    ('EL-6001', 'Bateria 70Ah 640A',                     2, 6, 118.00, 15, 4,  'Bateria de chumbo-ácido sem manutenção.'),
    ('EL-6002', 'Alternador 120A',                       2, 6, 245.70,  5, 2,  'Com regulador de tensão integrado.'),
    ('EL-6003', 'Motor de arranque',                     2, 6, 198.30,  4, 2,  'Testado em banco antes da expedição.'),
    ('TR-7001', 'Kit de embraiagem',                     4, 7, 312.00,  7, 2,  'Disco, prato e rolamento.'),
    ('TR-7002', 'Volante bimassa',                       4, 7, 429.90,  2, 1,  'Reduz vibrações e ruído na transmissão.'),
    ('AR-8001', 'Radiador de água',                      2, 8, 156.40,  9, 3,  'Núcleo em alumínio e caixas em plástico.'),
    ('AR-8002', 'Termostato com carcaça',                1, 8,  34.75, 28, 8,  'Abertura a 87 °C.'),
    ('AR-8003', 'Ventoinha do radiador',                 2, 8, 142.60,  6, 2,  'Inclui suporte e chicote elétrico.'),
    ('AR-8004', 'Anticongelante G12+ (5L)',              6, 8,  29.95, 44, 12, 'Pronto a usar, proteção até -37 °C.');

INSERT INTO clientes (nome, nif, email, telefone, morada) VALUES
    ('Oficina Silva & Filhos, Lda.', '501234567', 'geral@oficinasilva.pt',   '213456789', 'Rua das Oficinas 12, Lisboa'),
    ('Garagem Central Porto',        '502345678', 'compras@garagemcentral.pt','223456789', 'Av. da Boavista 340, Porto'),
    ('AutoRápido Coimbra',           '503456789', 'geral@autorapido.pt',      '239456789', 'Rua Nova 8, Coimbra'),
    ('Mecânica do Vale',             '504567890', 'info@mecanicadovale.pt',   '256456789', 'Estrada Nacional 1, Aveiro'),
    ('Transportes Nunes',            '505678901', 'frota@transportesnunes.pt','265456789', 'Zona Industrial Lote 5, Setúbal'),
    ('João Pereira Martins',         '206789012', 'joao.martins@email.pt',    '912345678', 'Rua do Sol 45, Braga'),
    ('Ana Sofia Ribeiro',            '207890123', 'ana.ribeiro@email.pt',     '933456789', 'Praceta das Flores 3, Faro'),
    ('Carlos Monteiro',              '208901234', 'carlos.monteiro@email.pt', '966789012', 'Rua da Estação 77, Viseu');

INSERT INTO metodos_pagamento (codigo, designacao, descricao, ativo, ordem) VALUES
    ('numerario',     'Numerário',                'Pagamento em dinheiro no balcão do armazém.',        1, 1),
    ('multibanco',    'Referência Multibanco',    'Gera entidade e referência com validade de 3 dias.', 1, 2),
    ('mbway',         'MB WAY',                   'Pedido enviado para o número de telemóvel indicado.',1, 3),
    ('cartao',        'Cartão de crédito/débito', 'Processado no terminal de pagamento automático.',    1, 4),
    ('transferencia', 'Transferência bancária',   'Requer envio do comprovativo com o IBAN indicado.',  1, 5),
    ('conta_corrente','Conta corrente',           'Apenas para clientes empresariais com crédito.',     1, 6);

-- Vendas de exemplo (histórico já fechado)
INSERT INTO vendas (numero, cliente_id, metodo_pagamento_id, referencia_pagamento, estado, total, observacoes, criada_em) VALUES
    ('V2026-0001', 1, 2, '21830 456 789 012', 'paga',     269.30, 'Entrega em mão no armazém.',  '2026-08-12 10:24:00'),
    ('V2026-0002', 3, 4, 'TPA-884512',        'paga',     143.80, NULL,                          '2026-08-19 15:02:00'),
    ('V2026-0003', 5, 6, 'CC-2026-0055',      'pendente', 858.00, 'Faturar a 30 dias.',          '2026-09-02 09:40:00');

INSERT INTO venda_itens (venda_id, peca_id, quantidade, preco_unitario, subtotal) VALUES
    (1, 1,  2,  64.90, 129.80),
    (1, 5,  4,   9.80,  39.20),
    (1, 17, 1,  56.80,  56.80),
    (1, 4,  2,  11.75,  23.50),
    (1, 7,  1,  22.30,  22.30),
    (2, 2,  1, 139.50, 139.50),
    (2, 5,  1,   9.80,   9.80),
    (3, 16, 1, 689.00, 689.00),
    (3, 13, 1, 179.00, 179.00);

UPDATE vendas v
SET v.total = (SELECT COALESCE(SUM(i.subtotal), 0) FROM venda_itens i WHERE i.venda_id = v.id);
