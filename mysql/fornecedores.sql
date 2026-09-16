CREATE TABLE fornecedores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  nome VARCHAR(140) NOT NULL,
  nif VARCHAR(20) NOT NULL UNIQUE,
  email VARCHAR(160) NOT NULL,
  telefone VARCHAR(30) NOT NULL,
  especialidade VARCHAR(100) NOT NULL
);

CREATE TABLE encomendas_fornecedores (
  id INT AUTO_INCREMENT PRIMARY KEY,
  fornecedor_id INT NOT NULL,
  referencia_peca VARCHAR(30) NOT NULL,
  nome_peca VARCHAR(140) NOT NULL,
  quantidade INT NOT NULL,
  observacoes TEXT,
  estado ENUM('pendente', 'enviada', 'recebida') NOT NULL DEFAULT 'pendente',
  criada_em TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
  FOREIGN KEY (fornecedor_id) REFERENCES fornecedores(id)
);

INSERT INTO fornecedores (nome, nif, email, telefone, especialidade) VALUES
  ('AutoParts Norte', '501111111', 'comercial@autoparts-norte.test', '221 100 200', 'Filtros e motor'),
  ('Travagem Pro', '502222222', 'encomendas@travagempro.test', '222 300 400', 'Travagem'),
  ('Eletrica Car', '503333333', 'apoio@eletricacar.test', '223 500 600', 'Baterias e iluminacao'),
  ('Suspensoes Iberia', '504444444', 'vendas@suspensoesiberia.test', '224 700 800', 'Suspensao');

INSERT INTO encomendas_fornecedores (fornecedor_id, referencia_peca, nome_peca, quantidade, observacoes, estado) VALUES
  (1, 'ALX-FIL-001', 'Filtro de oleo 1.6 TDI', 20, 'Repor stock para revisoes semanais.', 'pendente'),
  (2, 'ALX-TRV-014', 'Pastilhas de travao dianteiras', 10, 'Cliente com urgencia para frota.', 'enviada');
