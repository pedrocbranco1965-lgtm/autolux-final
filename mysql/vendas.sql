CREATE TABLE clientes (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(120) NOT NULL,
  email VARCHAR(160) NOT NULL UNIQUE,
  telefone VARCHAR(30) NOT NULL
);

CREATE TABLE pecas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  referencia VARCHAR(30) NOT NULL UNIQUE,
  nome VARCHAR(140) NOT NULL,
  marca VARCHAR(80) NOT NULL,
  tipo VARCHAR(80) NOT NULL,
  preco DECIMAL(10, 2) NOT NULL,
  stock INT NOT NULL DEFAULT 0,
  descricao TEXT NOT NULL
);

CREATE TABLE metodos_pagamento (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(80) NOT NULL UNIQUE,
  ativo TINYINT(1) NOT NULL DEFAULT 1
);

CREATE TABLE vendas (
  id INT AUTO_INCREMENT PRIMARY KEY,
  cliente_id INT NOT NULL,
  peca_id INT NOT NULL,
  metodo_pagamento_id INT NOT NULL,
  quantidade INT NOT NULL,
  total DECIMAL(10, 2) NOT NULL,
  criada_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (cliente_id) REFERENCES clientes(id),
  FOREIGN KEY (peca_id) REFERENCES pecas(id),
  FOREIGN KEY (metodo_pagamento_id) REFERENCES metodos_pagamento(id)
);

INSERT INTO clientes (nome, email, telefone) VALUES
  ('Ana Martins', 'ana.martins@example.com', '910 111 222'),
  ('Bruno Silva', 'bruno.silva@example.com', '920 333 444'),
  ('Carla Fernandes', 'carla.fernandes@example.com', '930 555 666');

INSERT INTO pecas (referencia, nome, marca, tipo, preco, stock, descricao) VALUES
  ('ALX-FIL-001', 'Filtro de oleo 1.6 TDI', 'Bosch', 'Filtros', 14.90, 45, 'Filtro de oleo compativel com motores diesel compactos.'),
  ('ALX-TRV-014', 'Pastilhas de travao dianteiras', 'Brembo', 'Travagem', 39.50, 28, 'Conjunto de pastilhas para eixo dianteiro com elevada durabilidade.'),
  ('ALX-BAT-022', 'Bateria 12V 70Ah', 'Varta', 'Eletrica', 119.00, 12, 'Bateria de arranque para veiculos ligeiros.'),
  ('ALX-LMP-007', 'Lampada H7 LED', 'Philips', 'Iluminacao', 24.99, 60, 'Lampada H7 LED branca para farois dianteiros.'),
  ('ALX-SUS-031', 'Amortecedor traseiro', 'Monroe', 'Suspensao', 72.75, 18, 'Amortecedor traseiro para conforto e estabilidade.'),
  ('ALX-COR-010', 'Correia de distribuicao', 'Continental', 'Motor', 56.40, 20, 'Correia reforcada para manutencao preventiva do motor.');

INSERT INTO metodos_pagamento (nome) VALUES
  ('Cartao Multibanco'),
  ('Transferencia Bancaria'),
  ('MB Way'),
  ('Dinheiro');
