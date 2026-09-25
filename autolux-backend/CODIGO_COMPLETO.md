# AutoLux Backend – código completo num único ficheiro

Gerado automaticamente a partir da pasta `autolux-backend/` para leitura no VS Code.
Cada secção corresponde a um ficheiro do projeto; o caminho está no título.

## Estrutura de pastas

```text
autolux-backend/
.env.example
.gitignore
README.txt
database/
    bd1_vendas.sql
    bd2_fornecedores.sql
node-api/
    server.js
    src/
        db.js
        httpError.js
        routes/
            encomendas.js
            fornecedores.js
package.json
php/
    config/
        env.php
        pagamentos.php
    public/
        assets/
            app.js
            estilos.css
        clientes.php
        encomenda_fornecedor.php
        encomendas_fornecedor.php
        fornecedores.php
        funcionarios.php
        index.php
        login.php
        logout.php
        pecas.php
        router.php
        venda.php
        venda_detalhe.php
        vendas.php
    src/
        Api/
            ApiException.php
            FornecedoresApiClient.php
        Auth.php
        Database.php
        Pagamentos/
            CartaoCredito.php
            Dinheiro.php
            MBWay.php
            MetodoPagamento.php
            Multibanco.php
            RegistoPagamentos.php
            Transferencia.php
        Repositorios/
            ClienteRepository.php
            FuncionarioRepository.php
            PecaRepository.php
            VendaRepository.php
        bootstrap.php
    templates/
        cabecalho.php
        erro.php
        rodape.php
scripts/
    setup-db.js
    test-api.js
```

## Índice

- [.env.example](#envexample)
- [.gitignore](#gitignore)
- [README.txt](#readmetxt)
- [database/bd1_vendas.sql](#databasebd1_vendassql)
- [database/bd2_fornecedores.sql](#databasebd2_fornecedoressql)
- [node-api/server.js](#node-apiserverjs)
- [node-api/src/db.js](#node-apisrcdbjs)
- [node-api/src/httpError.js](#node-apisrchttperrorjs)
- [node-api/src/routes/encomendas.js](#node-apisrcroutesencomendasjs)
- [node-api/src/routes/fornecedores.js](#node-apisrcroutesfornecedoresjs)
- [package.json](#packagejson)
- [php/config/env.php](#phpconfigenvphp)
- [php/config/pagamentos.php](#phpconfigpagamentosphp)
- [php/public/assets/app.js](#phppublicassetsappjs)
- [php/public/assets/estilos.css](#phppublicassetsestiloscss)
- [php/public/clientes.php](#phppublicclientesphp)
- [php/public/encomenda_fornecedor.php](#phppublicencomenda_fornecedorphp)
- [php/public/encomendas_fornecedor.php](#phppublicencomendas_fornecedorphp)
- [php/public/fornecedores.php](#phppublicfornecedoresphp)
- [php/public/funcionarios.php](#phppublicfuncionariosphp)
- [php/public/index.php](#phppublicindexphp)
- [php/public/login.php](#phppublicloginphp)
- [php/public/logout.php](#phppubliclogoutphp)
- [php/public/pecas.php](#phppublicpecasphp)
- [php/public/router.php](#phppublicrouterphp)
- [php/public/venda.php](#phppublicvendaphp)
- [php/public/venda_detalhe.php](#phppublicvenda_detalhephp)
- [php/public/vendas.php](#phppublicvendasphp)
- [php/src/Api/ApiException.php](#phpsrcapiapiexceptionphp)
- [php/src/Api/FornecedoresApiClient.php](#phpsrcapifornecedoresapiclientphp)
- [php/src/Auth.php](#phpsrcauthphp)
- [php/src/Database.php](#phpsrcdatabasephp)
- [php/src/Pagamentos/CartaoCredito.php](#phpsrcpagamentoscartaocreditophp)
- [php/src/Pagamentos/Dinheiro.php](#phpsrcpagamentosdinheirophp)
- [php/src/Pagamentos/MBWay.php](#phpsrcpagamentosmbwayphp)
- [php/src/Pagamentos/MetodoPagamento.php](#phpsrcpagamentosmetodopagamentophp)
- [php/src/Pagamentos/Multibanco.php](#phpsrcpagamentosmultibancophp)
- [php/src/Pagamentos/RegistoPagamentos.php](#phpsrcpagamentosregistopagamentosphp)
- [php/src/Pagamentos/Transferencia.php](#phpsrcpagamentostransferenciaphp)
- [php/src/Repositorios/ClienteRepository.php](#phpsrcrepositoriosclienterepositoryphp)
- [php/src/Repositorios/FuncionarioRepository.php](#phpsrcrepositoriosfuncionariorepositoryphp)
- [php/src/Repositorios/PecaRepository.php](#phpsrcrepositoriospecarepositoryphp)
- [php/src/Repositorios/VendaRepository.php](#phpsrcrepositoriosvendarepositoryphp)
- [php/src/bootstrap.php](#phpsrcbootstrapphp)
- [php/templates/cabecalho.php](#phptemplatescabecalhophp)
- [php/templates/erro.php](#phptemplateserrophp)
- [php/templates/rodape.php](#phptemplatesrodapephp)
- [scripts/setup-db.js](#scriptssetup-dbjs)
- [scripts/test-api.js](#scriptstest-apijs)

---

## .env.example

```text
# Copiar este ficheiro para ".env" e ajustar os valores ao vosso MySQL.
# O mesmo ficheiro é lido pelo Node.js (dotenv) e pelo PHP (php/config/env.php).

# --- Ligação ao servidor MySQL (partilhada pelas duas bases de dados) ---
DB_HOST=127.0.0.1
DB_PORT=3306
DB_USER=root
DB_PASSWORD=

# --- Base de Dados 1: clientes, peças/stock, vendas (PHP) ---
DB1_NAME=autolux_vendas

# --- Base de Dados 2: fornecedores e encomendas (Node.js) ---
DB2_NAME=autolux_fornecedores

# --- Web API Node.js ---
API_PORT=3000
# URL que o PHP usa para chamar a API (server-side, via cURL)
API_BASE_URL=http://localhost:3000/api

```

---

## .gitignore

```text
node_modules
.env

```

---

## README.txt

```text
AutoLux - Projeto Final de Backend (PHP + MySQL + Node.js)
===========================================================

Aluno: Pedro Castel-Branco

Projeto: AutoLux - Armazém de peças automóveis
Solução web para os funcionários consultarem stock e clientes, registarem
vendas de peças e encomendarem novas peças aos fornecedores.


1. ARQUITETURA
--------------

  +----------------------------+   PDO    +------------------------------+
  | Interface de Gestão (PHP)  | -------> | BD1 MySQL: autolux_vendas    |
  | php/public/*.php           |          | clientes, marcas, tipos_peca |
  |                            |          | pecas, vendas, vendas_itens  |
  |                            |          +------------------------------+
  |                            |
  |                            |  HTTP    +------------------------------+  mysql2  +------------------------------+
  |  (cURL, JSON)              | -------> | Web API Node.js (Express)    | -------> | BD2 MySQL:                   |
  |                            |          | node-api/server.js           |          | autolux_fornecedores         |
  |                            |          | /api/fornecedores            |          | fornecedores, encomendas,    |
  +----------------------------+          | /api/encomendas              |          | encomendas_itens             |
                                          +------------------------------+          +------------------------------+

  - Camada de Apresentação e Vendas (PHP 8): páginas dinâmicas + lógica de
    negócio de clientes, catálogo e registo de vendas. Liga diretamente à BD1.
  - Camada de Gestão de Fornecedores (Node.js): API REST dedicada ao módulo de
    compras. Recebe os pedidos da interface PHP e comunica com a BD2.
  - Camada de Armazenamento (MySQL): duas bases de dados independentes.

  O PHP NUNCA liga à BD2: todos os dados de fornecedores/encomendas passam
  obrigatoriamente pela API Node.js.


2. FONTE DE DADOS UTILIZADA
---------------------------

  Base de dados MySQL (BD1 "autolux_vendas", tabela "pecas"), acedida a partir
  do PHP por PDO com prepared statements. Os dados de exemplo (15 peças,
  8 marcas, 8 tipos, 5 clientes) são carregados pelo script database/bd1_vendas.sql.

  (Nota: o enunciado refere as opções A, B ou C mas a lista não está legível
  no PDF entregue; a versão implementada é a de base de dados MySQL, que é a
  exigida nos requisitos técnicos.)


3. REQUISITOS
-------------

  - Node.js 18 ou superior (testado com Node 22)      https://nodejs.org
  - PHP 8.1 ou superior com extensões pdo_mysql e curl (testado com PHP 8.3)
      Windows: XAMPP ou https://windows.php.net/download
      Ubuntu:  sudo apt install php-cli php-mysql php-curl php-mbstring
      macOS:   brew install php
  - MySQL 8 / MariaDB 10.x a correr localmente


4. INSTRUÇÕES PARA CORRER O PROJETO
-----------------------------------

  Todos os comandos são executados dentro da pasta autolux-backend/.

  1) Instalar dependências (Express, mysql2, dotenv, cors, concurrently):

       npm install

  2) Configurar o acesso ao MySQL:

       copiar .env.example para .env e editar DB_USER / DB_PASSWORD
       (Windows: copy .env.example .env  |  Linux/macOS: cp .env.example .env)

  3) Criar as duas bases de dados e carregar os dados de exemplo:

       npm run db:setup

     (alternativa manual: mysql -u root -p < database/bd1_vendas.sql
                          mysql -u root -p < database/bd2_fornecedores.sql)

  4) Arrancar tudo (API Node.js na porta 3000 + servidor PHP na porta 8000):

       npm start

     Abrir no browser:  http://localhost:8000  (aparece a página de login)

     Credenciais de teste:
       admin / admin123   -> administrador (acesso total + gestão de funcionários)
       ana   / ana123     -> funcionária (todas as operações exceto gerir funcionários)

     Para arrancar separadamente:
       npm run start:api   -> API Node.js   http://localhost:3000/api/health
       npm run start:php   -> Interface PHP http://localhost:8000

  5) (Opcional) Testar a API automaticamente com a API a correr:

       npm run test:api

  Nota XAMPP/Apache: em vez de "npm run start:php" pode apontar o DocumentRoot
  (ou um alias) para a pasta php/public. O ficheiro php/public/router.php só é
  usado pelo servidor embutido do PHP.


5. FUNCIONALIDADES IMPLEMENTADAS
--------------------------------

  A) Catálogo de peças (pecas.php)
     - Listagem das peças da BD1 com marca, tipo, referência, preço e stock.
     - Filtros por marca, tipo de peça e gama de preço (dropdown de gamas +
       preço mínimo/máximo livres), pesquisa por texto e "só stock baixo".
     - Filtros construídos dinamicamente em SQL com prepared statements
       (PecaRepository::listar).
     - Atalhos "Vender" e "Encomendar ao fornecedor" já pré-preenchidos.

  B) Formulário de venda a cliente (venda.php)
     - Dropdown de clientes (BD1).
     - Uma ou mais linhas de peça com informação da peça (referência, marca,
       tipo, preço, stock disponível, descrição) e resumo/total em tempo real.
     - Tipo de pagamento escolhido entre os métodos apresentados na página:
       Dinheiro, Referência Multibanco, MB WAY, Cartão, Transferência.
       EXTENSIBILIDADE: cada método é uma classe em php/src/Pagamentos/ que
       implementa a interface MetodoPagamento (código, nome, descrição, campo
       extra opcional, validação, detalhe a guardar). Para acrescentar um novo
       método basta criar a classe e registá-la em php/config/pagamentos.php.
       O formulário e a base de dados não precisam de alterações.
     - Validação no servidor (cliente, peças, quantidades, campo extra do
       pagamento) com mensagens junto aos campos.
     - Registo da venda numa transação (cabeçalho + itens + abate de stock com
       SELECT ... FOR UPDATE); se não houver stock a venda é recusada.
     - Histórico de vendas (vendas.php) e detalhe/recibo (venda_detalhe.php).

  C) Fornecedores e encomendas via Web API Node.js
     - fornecedores.php: lista obtida por GET /api/fornecedores; criação de
       fornecedor por POST /api/fornecedores.
     - encomenda_fornecedor.php: formulário de encomenda com dropdown de
       fornecedores (da API), linhas de peças (adicionadas do catálogo da BD1
       ou manualmente) e submissão por POST /api/encomendas.
     - encomendas_fornecedor.php: lista com filtro por estado
       (GET /api/encomendas), detalhe (GET /api/encomendas/:id) e alteração
       de estado (PATCH /api/encomendas/:id/estado).
     - Se a API estiver em baixo a interface mostra uma mensagem clara em vez
       de rebentar.

  Autenticação de funcionários
     - Todas as páginas exigem sessão iniciada (verificado centralmente em
       php/src/bootstrap.php); a única página pública é login.php.
     - login.php / logout.php com a classe AutoLux\Auth (sessão PHP):
         . passwords guardadas como hash bcrypt (password_hash / password_verify),
           nunca em claro;
         . session_regenerate_id() no login (contra session fixation);
         . bloqueio de 60 s após 5 tentativas falhadas (contra força bruta);
         . mensagem genérica "utilizador ou password incorretos";
         . redirect de volta à página pedida após o login (só para páginas internas).
     - Dois perfis: 'admin' e 'funcionario'. A página funcionarios.php (criar,
       ativar/desativar, redefinir password) é exclusiva do admin
       (Auth::exigirAdmin()). Não é possível desativar a própria conta nem o
       último administrador ativo; contas desativadas não conseguem entrar.
     - Cada venda guarda o funcionário que a registou (vendas.funcionario_id)
       e cada encomenda a fornecedor leva o nome de quem a submeteu
       (campo criado_por, enviado pelo PHP para a API Node.js).
     - Proteção CSRF: todos os formulários POST incluem um token de sessão
       (campoCsrf()) validado automaticamente no bootstrap; o logout é por POST.

  Extras
     - Painel inicial (index.php) com indicadores das duas bases de dados e
       alerta de peças abaixo do stock mínimo.
     - Gestão de clientes (clientes.php) com pesquisa e criação.
     - Mensagens "flash" após cada operação, escape de HTML em todos os
       outputs (função e()) e página de erro amigável.


6. WEB API NODE.JS - ENDPOINTS
------------------------------

  GET    /api/health                      estado do serviço e ligação à BD2
  GET    /api/fornecedores[?todos=1]      lista fornecedores (ativos por defeito)
  GET    /api/fornecedores/:id            detalhe de fornecedor
  POST   /api/fornecedores                cria fornecedor
                                          { nome, nif, email, telefone?, morada?, prazo_entrega_dias? }
  GET    /api/encomendas[?estado=&fornecedor_id=]  lista encomendas
  GET    /api/encomendas/:id              encomenda com itens
  POST   /api/encomendas                  cria encomenda (transação)
                                          { fornecedor_id, observacoes?, criado_por?, itens:[{referencia_peca, descricao, quantidade, preco_unitario}] }
  PATCH  /api/encomendas/:id/estado       { estado: pendente|enviada|recebida|cancelada }

  Erros devolvidos em JSON: { "erro": "...", "detalhes": [...] } com o código
  HTTP adequado (400 validação, 404 não encontrado, 409 duplicado, 422 regra
  de negócio, 500 interno).


7. ESTRUTURA DE PASTAS
----------------------

  autolux-backend/
    CODIGO_COMPLETO.md        todo o código-fonte num só ficheiro (para leitura/estudo)
    package.json              scripts npm (install / start / db:setup / test:api)
    .env.example              configuração (copiar para .env)
    database/
      bd1_vendas.sql          BD1: funcionarios, clientes, marcas, tipos_peca, pecas, vendas, vendas_itens
      bd2_fornecedores.sql    BD2: fornecedores, encomendas, encomendas_itens
    scripts/
      setup-db.js             executa os .sql (npm run db:setup)
      test-api.js             testes da API (npm run test:api)
    node-api/
      server.js               arranque do Express, middlewares, rotas, erros
      src/db.js               pool mysql2 para a BD2
      src/httpError.js        erro com código HTTP
      src/routes/fornecedores.js
      src/routes/encomendas.js
    php/
      public/                 páginas acessíveis pelo browser
        login.php             autenticação (única página pública)
        logout.php            terminar sessão (POST)
        funcionarios.php      gestão de funcionários (só admin)
        index.php             painel
        pecas.php             A) catálogo com filtros
        clientes.php          clientes (lista + novo)
        venda.php             B) formulário de venda
        vendas.php            histórico de vendas
        venda_detalhe.php     recibo
        fornecedores.php      C) lista de fornecedores (API)
        encomenda_fornecedor.php   C) submissão de encomenda (API)
        encomendas_fornecedor.php  lista/detalhe/estado de encomendas (API)
        router.php            router do servidor embutido do PHP
        assets/estilos.css, assets/app.js
      src/
        bootstrap.php         config, autoload, sessão, CSRF, autenticação, funções auxiliares
        Database.php          ligação PDO (singleton) à BD1
        Auth.php              login/logout, sessão, perfis, bloqueio de tentativas
        Repositorios/         PecaRepository, ClienteRepository, VendaRepository,
                              FuncionarioRepository
        Pagamentos/           MetodoPagamento (interface) + Dinheiro, Multibanco,
                              MBWay, CartaoCredito, Transferencia, RegistoPagamentos
        Api/                  FornecedoresApiClient (cURL) + ApiException
      config/
        env.php               leitura do .env
        pagamentos.php        registo dos métodos de pagamento
      templates/              cabecalho.php, rodape.php, erro.php


8. NOTAS
--------

  - Os nomes das bases de dados estão fixos nos ficheiros .sql
    (autolux_vendas e autolux_fornecedores). Se os alterar, altere também
    DB1_NAME / DB2_NAME no .env.
  - Não é usado nenhum framework PHP nem ORM, para ficar claro como se faz
    "à mão": PDO, prepared statements, transações, autoload PSR-4 simples,
    templates com include e separação páginas / repositórios / serviços.

```

---

## database/bd1_vendas.sql

```sql
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
DROP TABLE IF EXISTS funcionarios;

-- ---------------------------------------------------------------------
-- Funcionários (utilizadores da interface de gestão)
-- A password é guardada como hash bcrypt (PHP password_hash / password_verify).
-- perfil: 'admin' gere funcionários; 'funcionario' usa a aplicação.
-- ---------------------------------------------------------------------
CREATE TABLE funcionarios (
  id             INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
  nome           VARCHAR(120) NOT NULL,
  utilizador     VARCHAR(40)  NOT NULL UNIQUE,
  email          VARCHAR(160) NOT NULL,
  password_hash  VARCHAR(255) NOT NULL,
  perfil         ENUM('admin','funcionario') NOT NULL DEFAULT 'funcionario',
  ativo          TINYINT(1)   NOT NULL DEFAULT 1,
  ultimo_login   DATETIME     NULL,
  criado_em      TIMESTAMP    NOT NULL DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB;

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
  funcionario_id  INT UNSIGNED  NULL,          -- quem registou a venda
  data_venda      DATETIME      NOT NULL DEFAULT CURRENT_TIMESTAMP,
  tipo_pagamento  VARCHAR(40)   NOT NULL,
  detalhe_pagamento VARCHAR(160) NULL,
  total           DECIMAL(10,2) NOT NULL DEFAULT 0,
  observacoes     VARCHAR(255)  NULL,
  CONSTRAINT fk_vendas_cliente     FOREIGN KEY (cliente_id)     REFERENCES clientes(id),
  CONSTRAINT fk_vendas_funcionario FOREIGN KEY (funcionario_id) REFERENCES funcionarios(id) ON DELETE SET NULL
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

-- Funcionários de teste (passwords: admin -> admin123 | ana -> ana123)
INSERT INTO funcionarios (nome, utilizador, email, password_hash, perfil) VALUES
  ('Administrador', 'admin', 'admin@autolux.pt',      '$2y$10$CSDfjJcRWNvAHxHjJaB7Ne1ASfRiqwMR9OeBKmIkWCskBdpNJl/QS', 'admin'),
  ('Ana Martins',   'ana',   'ana.martins@autolux.pt', '$2y$10$VAzNiVDj3ehtIAJq2nkcNuQPVDmvZUO293H4hPMQx2M4LRjhDhzR.', 'funcionario');

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
INSERT INTO vendas (cliente_id, funcionario_id, tipo_pagamento, detalhe_pagamento, total, observacoes)
VALUES (1, 2, 'multibanco', 'Ref. 123 456 789', 95.55, 'Venda de demonstração');

INSERT INTO vendas_itens (venda_id, peca_id, quantidade, preco_unitario) VALUES
  (1, (SELECT id FROM pecas WHERE referencia='BR-0986494'), 2, 42.90),
  (1, (SELECT id FROM pecas WHERE referencia='FL-W71230'),  1, 9.75);

```

---

## database/bd2_fornecedores.sql

```sql
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

```

---

## node-api/server.js

```javascript
/**
 * AutoLux - Web API de Fornecedores e Encomendas (Node.js + Express)
 *
 * Camada intermédia dedicada ao módulo de compras. Recebe pedidos HTTP da
 * interface de gestão em PHP e comunica com a Base de Dados 2 (MySQL).
 *
 * Arrancar:  npm run start:api   (ou node node-api/server.js)
 */
const path = require('path');
require('dotenv').config({ path: path.join(__dirname, '..', '.env') });

const express = require('express');
const cors = require('cors');

const pool = require('./src/db');
const HttpError = require('./src/httpError');
const fornecedoresRouter = require('./src/routes/fornecedores');
const encomendasRouter = require('./src/routes/encomendas');

const app = express();
const PORT = Number(process.env.API_PORT || 3000);

app.use(cors());
app.use(express.json());

// Log simples de cada pedido (útil para demonstrar a integração PHP -> Node)
app.use((req, _res, next) => {
  console.log(`${new Date().toISOString()} ${req.method} ${req.originalUrl}`);
  next();
});

app.get('/api/health', async (_req, res) => {
  try {
    await pool.query('SELECT 1');
    res.json({ estado: 'ok', servico: 'autolux-fornecedores-api', bd: process.env.DB2_NAME || 'autolux_fornecedores' });
  } catch (erro) {
    res.status(503).json({ estado: 'erro', mensagem: 'Sem ligação à base de dados', detalhe: erro.message });
  }
});

app.use('/api/fornecedores', fornecedoresRouter);
app.use('/api/encomendas', encomendasRouter);

// 404 para rotas desconhecidas
app.use((req, _res, next) => next(new HttpError(404, `Rota não encontrada: ${req.method} ${req.originalUrl}`)));

// Middleware central de erros: qualquer erro lançado nas rotas acaba aqui
// eslint-disable-next-line no-unused-vars
app.use((erro, _req, res, _next) => {
  const status = erro.status || 500;
  if (status >= 500) console.error(erro);
  res.status(status).json({
    erro: erro.message || 'Erro interno',
    ...(erro.detalhes ? { detalhes: erro.detalhes } : {}),
  });
});

app.listen(PORT, () => {
  console.log(`API de fornecedores a escutar em http://localhost:${PORT}/api`);
});

```

---

## node-api/src/db.js

```javascript
/**
 * Pool de ligações à Base de Dados 2 (fornecedores e encomendas).
 * Um pool reutiliza ligações em vez de abrir uma nova por pedido.
 */
const mysql = require('mysql2/promise');

const pool = mysql.createPool({
  host: process.env.DB_HOST || '127.0.0.1',
  port: Number(process.env.DB_PORT || 3306),
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASSWORD || '',
  database: process.env.DB2_NAME || 'autolux_fornecedores',
  waitForConnections: true,
  connectionLimit: 10,
  // DECIMAL vem como string por defeito; convertemos para número para o JSON.
  decimalNumbers: true,
});

module.exports = pool;

```

---

## node-api/src/httpError.js

```javascript
/**
 * Erro com código HTTP associado. Permite às rotas lançarem
 * `throw new HttpError(404, 'Fornecedor não encontrado')` e deixar o
 * middleware de erros em server.js formatar a resposta.
 */
class HttpError extends Error {
  constructor(status, message, detalhes) {
    super(message);
    this.status = status;
    this.detalhes = detalhes;
  }
}

module.exports = HttpError;

```

---

## node-api/src/routes/encomendas.js

```javascript
const express = require('express');
const pool = require('../db');
const HttpError = require('../httpError');

const router = express.Router();

const ESTADOS = ['pendente', 'enviada', 'recebida', 'cancelada'];

/** Carrega uma encomenda com o nome do fornecedor e as respetivas linhas. */
async function obterEncomenda(id, ligacao = pool) {
  const [cabecalho] = await ligacao.query(
    `SELECT e.id, e.fornecedor_id, f.nome AS fornecedor_nome, e.data_encomenda,
            e.estado, e.total, e.observacoes, e.criado_por
       FROM encomendas e
       JOIN fornecedores f ON f.id = e.fornecedor_id
      WHERE e.id = ?`,
    [id]
  );
  if (cabecalho.length === 0) return null;

  const [itens] = await ligacao.query(
    `SELECT id, referencia_peca, descricao, quantidade, preco_unitario,
            quantidade * preco_unitario AS subtotal
       FROM encomendas_itens WHERE encomenda_id = ? ORDER BY id`,
    [id]
  );
  return { ...cabecalho[0], itens };
}

/**
 * GET /api/encomendas?estado=pendente&fornecedor_id=1
 */
router.get('/', async (req, res, next) => {
  try {
    const condicoes = [];
    const params = [];
    if (req.query.estado) {
      if (!ESTADOS.includes(req.query.estado)) throw new HttpError(400, `estado inválido (${ESTADOS.join(', ')})`);
      condicoes.push('e.estado = ?');
      params.push(req.query.estado);
    }
    if (req.query.fornecedor_id) {
      condicoes.push('e.fornecedor_id = ?');
      params.push(Number(req.query.fornecedor_id));
    }
    const where = condicoes.length ? `WHERE ${condicoes.join(' AND ')}` : '';

    const [linhas] = await pool.query(
      `SELECT e.id, e.fornecedor_id, f.nome AS fornecedor_nome, e.data_encomenda,
              e.estado, e.total, e.observacoes, e.criado_por,
              COUNT(i.id) AS num_linhas, COALESCE(SUM(i.quantidade), 0) AS total_unidades
         FROM encomendas e
         JOIN fornecedores f ON f.id = e.fornecedor_id
         LEFT JOIN encomendas_itens i ON i.encomenda_id = e.id
         ${where}
        GROUP BY e.id
        ORDER BY e.data_encomenda DESC, e.id DESC`,
      params
    );
    res.json(linhas);
  } catch (erro) {
    next(erro);
  }
});

/**
 * GET /api/encomendas/:id  (com itens)
 */
router.get('/:id', async (req, res, next) => {
  try {
    const encomenda = await obterEncomenda(req.params.id);
    if (!encomenda) throw new HttpError(404, 'Encomenda não encontrada');
    res.json(encomenda);
  } catch (erro) {
    next(erro);
  }
});

/**
 * POST /api/encomendas
 * Body JSON:
 * {
 *   "fornecedor_id": 1,
 *   "observacoes": "texto opcional",
 *   "criado_por": "nome do funcionário (opcional)",
 *   "itens": [
 *     { "referencia_peca": "BR-0986494", "descricao": "Pastilhas", "quantidade": 10, "preco_unitario": 28.6 }
 *   ]
 * }
 * Cabeçalho + linhas são gravados numa transação: ou fica tudo ou nada.
 */
router.post('/', async (req, res, next) => {
  const { fornecedor_id, observacoes, itens, criado_por } = req.body || {};
  const erros = [];

  if (!Number.isInteger(Number(fornecedor_id)) || Number(fornecedor_id) <= 0) erros.push('fornecedor_id inválido');
  if (!Array.isArray(itens) || itens.length === 0) erros.push('a encomenda precisa de pelo menos um item');
  else {
    itens.forEach((item, i) => {
      if (!item.referencia_peca) erros.push(`itens[${i}].referencia_peca é obrigatória`);
      if (!item.descricao) erros.push(`itens[${i}].descricao é obrigatória`);
      if (!Number.isInteger(Number(item.quantidade)) || Number(item.quantidade) <= 0) erros.push(`itens[${i}].quantidade deve ser inteiro > 0`);
      if (Number.isNaN(Number(item.preco_unitario)) || Number(item.preco_unitario) < 0) erros.push(`itens[${i}].preco_unitario inválido`);
    });
  }
  if (erros.length) return next(new HttpError(400, 'Dados inválidos', erros));

  const ligacao = await pool.getConnection();
  try {
    await ligacao.beginTransaction();

    const [fornecedor] = await ligacao.query('SELECT id, ativo FROM fornecedores WHERE id = ?', [fornecedor_id]);
    if (fornecedor.length === 0) throw new HttpError(404, 'Fornecedor não encontrado');
    if (!fornecedor[0].ativo) throw new HttpError(422, 'Fornecedor inativo');

    const total = itens.reduce((soma, it) => soma + Number(it.quantidade) * Number(it.preco_unitario), 0);

    const [cab] = await ligacao.query(
      'INSERT INTO encomendas (fornecedor_id, total, observacoes, criado_por) VALUES (?, ?, ?, ?)',
      [
        fornecedor_id,
        total.toFixed(2),
        observacoes ? String(observacoes).slice(0, 255) : null,
        criado_por ? String(criado_por).slice(0, 120) : null,
      ]
    );

    const valores = itens.map((it) => [
      cab.insertId,
      String(it.referencia_peca).trim(),
      String(it.descricao).trim().slice(0, 160),
      Number(it.quantidade),
      Number(it.preco_unitario).toFixed(2),
    ]);
    await ligacao.query(
      'INSERT INTO encomendas_itens (encomenda_id, referencia_peca, descricao, quantidade, preco_unitario) VALUES ?',
      [valores]
    );

    await ligacao.commit();
    const encomenda = await obterEncomenda(cab.insertId);
    res.status(201).json(encomenda);
  } catch (erro) {
    await ligacao.rollback();
    next(erro);
  } finally {
    ligacao.release();
  }
});

/**
 * PATCH /api/encomendas/:id/estado
 * Body JSON: { "estado": "enviada" }
 */
router.patch('/:id/estado', async (req, res, next) => {
  try {
    const { estado } = req.body || {};
    if (!ESTADOS.includes(estado)) throw new HttpError(400, `estado inválido (${ESTADOS.join(', ')})`);

    const [resultado] = await pool.query('UPDATE encomendas SET estado = ? WHERE id = ?', [estado, req.params.id]);
    if (resultado.affectedRows === 0) throw new HttpError(404, 'Encomenda não encontrada');

    res.json(await obterEncomenda(req.params.id));
  } catch (erro) {
    next(erro);
  }
});

module.exports = router;

```

---

## node-api/src/routes/fornecedores.js

```javascript
const express = require('express');
const pool = require('../db');
const HttpError = require('../httpError');

const router = express.Router();

/**
 * GET /api/fornecedores
 * Lista os fornecedores. Por defeito só os ativos; ?todos=1 inclui inativos.
 */
router.get('/', async (req, res, next) => {
  try {
    const incluirInativos = req.query.todos === '1';
    const sql = `
      SELECT f.id, f.nome, f.nif, f.email, f.telefone, f.morada,
             f.prazo_entrega_dias, f.ativo, f.criado_em,
             COUNT(e.id) AS total_encomendas
        FROM fornecedores f
        LEFT JOIN encomendas e ON e.fornecedor_id = f.id
       ${incluirInativos ? '' : 'WHERE f.ativo = 1'}
       GROUP BY f.id
       ORDER BY f.nome`;
    const [linhas] = await pool.query(sql);
    res.json(linhas);
  } catch (erro) {
    next(erro);
  }
});

/**
 * GET /api/fornecedores/:id
 */
router.get('/:id', async (req, res, next) => {
  try {
    const [linhas] = await pool.query('SELECT * FROM fornecedores WHERE id = ?', [req.params.id]);
    if (linhas.length === 0) throw new HttpError(404, 'Fornecedor não encontrado');
    res.json(linhas[0]);
  } catch (erro) {
    next(erro);
  }
});

/**
 * POST /api/fornecedores
 * Body JSON: { nome, nif, email, telefone?, morada?, prazo_entrega_dias? }
 */
router.post('/', async (req, res, next) => {
  try {
    const { nome, nif, email, telefone, morada, prazo_entrega_dias } = req.body || {};
    const erros = [];
    if (!nome || String(nome).trim().length < 2) erros.push('nome é obrigatório');
    if (!/^\d{9}$/.test(String(nif || ''))) erros.push('nif deve ter 9 dígitos');
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) erros.push('email inválido');
    if (erros.length) throw new HttpError(400, 'Dados inválidos', erros);

    const [resultado] = await pool.query(
      `INSERT INTO fornecedores (nome, nif, email, telefone, morada, prazo_entrega_dias)
       VALUES (?, ?, ?, ?, ?, ?)`,
      [nome.trim(), nif, email.trim(), telefone || null, morada || null, Number(prazo_entrega_dias) || 5]
    );
    const [linhas] = await pool.query('SELECT * FROM fornecedores WHERE id = ?', [resultado.insertId]);
    res.status(201).json(linhas[0]);
  } catch (erro) {
    if (erro.code === 'ER_DUP_ENTRY') return next(new HttpError(409, 'Já existe um fornecedor com esse NIF'));
    next(erro);
  }
});

module.exports = router;

```

---

## package.json

```json
{
  "name": "autolux-backend",
  "version": "1.0.0",
  "private": true,
  "description": "AutoLux - Gestão de armazém de peças automóveis (PHP + MySQL + Node.js)",
  "main": "node-api/server.js",
  "scripts": {
    "db:setup": "node scripts/setup-db.js",
    "start:api": "node node-api/server.js",
    "start:php": "php -S localhost:8000 -t php/public php/public/router.php",
    "start": "concurrently -n api,php -c blue,magenta \"npm run start:api\" \"npm run start:php\"",
    "dev": "concurrently -n api,php -c blue,magenta \"node --watch node-api/server.js\" \"npm run start:php\"",
    "test:api": "node scripts/test-api.js"
  },
  "engines": {
    "node": ">=18"
  },
  "dependencies": {
    "concurrently": "^9.1.2",
    "cors": "^2.8.5",
    "dotenv": "^16.4.7",
    "express": "^4.21.2",
    "mysql2": "^3.12.0"
  }
}

```

---

## php/config/env.php

```php
<?php
/**
 * Lê o ficheiro .env da raiz do projeto (o mesmo usado pelo Node.js) e
 * devolve um array com a configuração. Se o .env não existir, usa os
 * valores por defeito, para o projeto arrancar "à primeira".
 */
declare(strict_types=1);

function carregarConfiguracao(): array
{
    $defaults = [
        'DB_HOST'      => '127.0.0.1',
        'DB_PORT'      => '3306',
        'DB_USER'      => 'root',
        'DB_PASSWORD'  => '',
        'DB1_NAME'     => 'autolux_vendas',
        'API_BASE_URL' => 'http://localhost:3000/api',
    ];

    $ficheiro = dirname(__DIR__, 2) . '/.env';
    if (!is_readable($ficheiro)) {
        return $defaults;
    }

    foreach (file($ficheiro, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linha) {
        $linha = trim($linha);
        if ($linha === '' || str_starts_with($linha, '#') || !str_contains($linha, '=')) {
            continue;
        }
        [$chave, $valor] = explode('=', $linha, 2);
        $defaults[trim($chave)] = trim($valor, " \t\"'");
    }

    return $defaults;
}

return carregarConfiguracao();

```

---

## php/config/pagamentos.php

```php
<?php
/**
 * Métodos de pagamento disponíveis no formulário de venda.
 *
 * EXTENSIBILIDADE: para introduzir um novo método (ex.: PayPal), cria-se a
 * classe AutoLux\Pagamentos\PayPal que implementa MetodoPagamento e
 * acrescenta-se uma linha a esta lista. A ordem aqui é a ordem na página.
 */
declare(strict_types=1);

use AutoLux\Pagamentos;

return [
    Pagamentos\Dinheiro::class,
    Pagamentos\Multibanco::class,
    Pagamentos\MBWay::class,
    Pagamentos\CartaoCredito::class,
    Pagamentos\Transferencia::class,
];

```

---

## php/public/assets/app.js

```javascript
/**
 * JavaScript de apoio às páginas PHP (melhora a experiência, mas os
 * formulários funcionam sem ele porque a validação real é feita no servidor).
 *
 *  - venda.php: mostra informação da peça escolhida, linhas dinâmicas,
 *    total em tempo real e campo extra consoante o método de pagamento.
 *  - encomenda_fornecedor.php: adicionar peças do catálogo, linhas dinâmicas e total.
 */
(function () {
  'use strict';

  const dadosPecas = document.getElementById('dados-pecas');
  const pecas = dadosPecas ? JSON.parse(dadosPecas.textContent) : [];
  const pecaPorId = new Map(pecas.map((p) => [String(p.id), p]));

  const euros = (v) => new Intl.NumberFormat('pt-PT', { style: 'currency', currency: 'EUR' }).format(v || 0);

  // ---------------------------------------------------------------- venda.php
  const formVenda = document.getElementById('form-venda');
  if (formVenda) {
    const contentor = document.getElementById('linhas-pecas');
    const template = document.getElementById('template-linha');
    const resumoItens = document.getElementById('resumo-itens');
    const resumoTotal = document.getElementById('resumo-total');

    function atualizarLinha(linha) {
      const select = linha.querySelector('.select-peca');
      const qtd = linha.querySelector('.input-quantidade');
      const info = linha.querySelector('.info-peca');
      const peca = pecaPorId.get(select.value);
      if (!peca) {
        info.textContent = '';
        return;
      }
      qtd.max = peca.stock;
      if (Number(qtd.value) > peca.stock) qtd.value = peca.stock;
      info.innerHTML =
        `<b>${peca.marca} ${peca.nome}</b> · ${peca.tipo} · ref. <code>${peca.referencia}</code> · ` +
        `${euros(peca.preco)}/un · stock disponível <b>${peca.stock}</b>` +
        (peca.descricao ? `<br>${peca.descricao}` : '');
    }

    function atualizarResumo() {
      const linhas = [...contentor.querySelectorAll('.linha-peca')];
      let total = 0;
      const itens = [];
      linhas.forEach((linha) => {
        const peca = pecaPorId.get(linha.querySelector('.select-peca').value);
        const qtd = Number(linha.querySelector('.input-quantidade').value) || 0;
        if (peca && qtd > 0) {
          total += peca.preco * qtd;
          itens.push(`<li><span>${qtd} × ${peca.nome}</span><span>${euros(peca.preco * qtd)}</span></li>`);
        }
      });
      resumoItens.innerHTML = itens.length ? itens.join('') : '<li class="texto-suave">Nenhuma peça selecionada.</li>';
      resumoTotal.textContent = euros(total);
      contentor.querySelectorAll('.remover-linha').forEach((b) => (b.disabled = linhas.length === 1));
    }

    contentor.addEventListener('input', (ev) => {
      const linha = ev.target.closest('.linha-peca');
      if (linha) atualizarLinha(linha);
      atualizarResumo();
    });
    contentor.addEventListener('click', (ev) => {
      if (ev.target.classList.contains('remover-linha')) {
        ev.target.closest('.linha-peca').remove();
        atualizarResumo();
      }
    });
    document.getElementById('adicionar-linha').addEventListener('click', () => {
      contentor.appendChild(template.content.cloneNode(true));
      atualizarResumo();
    });

    contentor.querySelectorAll('.linha-peca').forEach(atualizarLinha);
    atualizarResumo();

    // Campo extra do método de pagamento (telemóvel MB WAY, dígitos do cartão...)
    const grupoExtra = document.getElementById('grupo-campo-extra');
    const rotuloExtra = document.getElementById('rotulo-campo-extra');
    function atualizarCampoExtra() {
      const escolhido = formVenda.querySelector('input[name="tipo_pagamento"]:checked');
      const etiqueta = escolhido ? escolhido.dataset.campoExtra : '';
      grupoExtra.hidden = !etiqueta;
      rotuloExtra.textContent = etiqueta;
    }
    formVenda.querySelectorAll('input[name="tipo_pagamento"]').forEach((r) => r.addEventListener('change', atualizarCampoExtra));
    atualizarCampoExtra();
  }

  // -------------------------------------------------- encomenda_fornecedor.php
  const formEncomenda = document.getElementById('form-encomenda');
  if (formEncomenda) {
    const contentor = document.getElementById('linhas-encomenda');
    const template = document.getElementById('template-linha-encomenda');
    const resumoTotal = document.getElementById('resumo-total');
    const seletor = document.getElementById('seletor-catalogo');

    function atualizarTotal() {
      let total = 0;
      contentor.querySelectorAll('.linha-encomenda').forEach((l) => {
        const qtd = Number(l.querySelector('.input-quantidade').value) || 0;
        const preco = Number(l.querySelector('.input-preco').value) || 0;
        total += qtd * preco;
      });
      resumoTotal.textContent = euros(total);
      const linhas = contentor.querySelectorAll('.linha-encomenda');
      contentor.querySelectorAll('.remover-linha').forEach((b) => (b.disabled = linhas.length === 1));
    }

    function novaLinha(valores) {
      const fragmento = template.content.cloneNode(true);
      const linha = fragmento.querySelector('.linha-encomenda');
      if (valores) {
        linha.querySelector('[name="referencia[]"]').value = valores.referencia;
        linha.querySelector('[name="descricao[]"]').value = valores.descricao;
        linha.querySelector('[name="quantidade[]"]').value = valores.quantidade;
        linha.querySelector('[name="preco_unitario[]"]').value = valores.preco_unitario;
      }
      // Se a única linha existente estiver vazia, substitui-a em vez de acrescentar
      const existentes = [...contentor.querySelectorAll('.linha-encomenda')];
      const vazia = existentes.length === 1 && !existentes[0].querySelector('[name="referencia[]"]').value;
      if (vazia && valores) existentes[0].remove();
      contentor.appendChild(fragmento);
      atualizarTotal();
    }

    seletor.addEventListener('change', () => {
      const peca = pecaPorId.get(seletor.value);
      if (peca) {
        novaLinha({
          referencia: peca.referencia,
          descricao: peca.descricao,
          quantidade: Math.max(1, peca.stock_minimo * 2 - peca.stock),
          preco_unitario: peca.preco_custo.toFixed(2),
        });
      }
      seletor.value = '';
    });
    document.getElementById('adicionar-linha-vazia').addEventListener('click', () => novaLinha(null));
    contentor.addEventListener('input', atualizarTotal);
    contentor.addEventListener('click', (ev) => {
      if (ev.target.classList.contains('remover-linha')) {
        ev.target.closest('.linha-encomenda').remove();
        atualizarTotal();
      }
    });
    atualizarTotal();
  }
})();

```

---

## php/public/assets/estilos.css

```css
:root {
  --fundo: #f4f6f8;
  --superficie: #ffffff;
  --superficie-suave: #eef2f6;
  --texto: #151a22;
  --suave: #647080;
  --primaria: #0b1f3a;
  --primaria-clara: #173b68;
  --acento: #c9a35d;
  --borda: #dbe2ea;
  --sucesso: #126c43;
  --aviso: #9a6700;
  --erro: #b42318;
  --info: #1d4ed8;
  --sombra: 0 10px 35px rgba(11, 31, 58, 0.08);
  font-family: Inter, system-ui, -apple-system, "Segoe UI", sans-serif;
}

* { box-sizing: border-box; }
body { margin: 0; background: var(--fundo); color: var(--texto); line-height: 1.45; }
a { color: var(--primaria); text-decoration: none; }
a:hover { text-decoration: underline; }
code { background: var(--superficie-suave); padding: 2px 6px; border-radius: 6px; font-size: 0.88em; }
button, input, select, textarea { font: inherit; }
h1, h2, h3 { color: var(--primaria); margin: 0 0 8px; line-height: 1.15; }
h1 { font-size: clamp(1.7rem, 3vw, 2.4rem); }
h2 { font-size: 1.15rem; }
h3 { font-size: 1.05rem; }

/* ---------- Topo / menu ---------- */
.topo {
  position: sticky; top: 0; z-index: 10;
  display: flex; align-items: center; justify-content: space-between; gap: 24px; flex-wrap: wrap;
  padding: 14px 28px; background: rgba(255, 255, 255, 0.95); border-bottom: 1px solid var(--borda);
  backdrop-filter: blur(12px);
}
.marca { display: flex; align-items: center; gap: 10px; font-weight: 800; font-size: 1.15rem; color: var(--primaria); }
.marca:hover { text-decoration: none; }
.marca small { font-weight: 500; color: var(--suave); font-size: 0.8rem; }
.marca-simbolo { display: grid; place-items: center; width: 36px; height: 36px; border-radius: 12px; background: var(--primaria); color: #fff; font-size: 0.85rem; }
.menu { display: flex; gap: 6px; flex-wrap: wrap; }
.menu a { padding: 8px 12px; border-radius: 10px; color: var(--suave); font-weight: 600; }
.menu a:hover { background: var(--superficie-suave); text-decoration: none; color: var(--primaria); }
.menu a.ativo { background: var(--primaria); color: #fff; }

.sessao { display: flex; align-items: center; gap: 12px; margin-left: auto; }
.sessao-nome { display: grid; text-align: right; font-weight: 700; color: var(--primaria); font-size: 0.9rem; line-height: 1.2; }
.sessao-nome small { color: var(--suave); font-weight: 500; font-size: 0.75rem; }
.sessao form { margin: 0; }

/* ---------- Login ---------- */
.pagina-login { min-height: 100vh; display: grid; place-items: center; padding: 24px; background: linear-gradient(135deg, var(--primaria) 0%, var(--primaria-clara) 60%, #2d5a8e 100%); }
.cartao-login { width: min(420px, 100%); padding: 32px; background: var(--superficie); border-radius: 24px; box-shadow: 0 25px 60px rgba(0, 0, 0, 0.3); display: grid; gap: 12px; }
.cartao-login .marca { margin-bottom: 10px; }
.cartao-login h1 { margin-bottom: 8px; }
.credenciais-teste { margin-top: 8px; font-size: 0.85rem; color: var(--suave); }
.credenciais-teste summary { cursor: pointer; font-weight: 700; }
.credenciais-teste ul { margin: 8px 0 0; padding-left: 18px; display: grid; gap: 4px; }

/* ---------- Popover (nova password) ---------- */
.acoes-inline { flex-wrap: nowrap; }
.acoes-inline form { margin: 0; }
.popover { position: relative; }
.popover summary { list-style: none; }
.popover summary::-webkit-details-marker { display: none; }
.popover-conteudo { position: absolute; right: 0; top: calc(100% + 6px); z-index: 5; width: 260px; padding: 14px; background: var(--superficie); border: 1px solid var(--borda); border-radius: 14px; box-shadow: var(--sombra); }

/* ---------- Layout ---------- */
.conteudo { width: min(1200px, calc(100% - 32px)); margin: 0 auto; padding: 32px 0 60px; }
.cabecalho-pagina { display: flex; justify-content: space-between; align-items: flex-end; gap: 20px; flex-wrap: wrap; margin-bottom: 22px; }
.cabecalho-pagina p { color: var(--suave); margin: 4px 0 0; }
.rotulo { margin: 0; color: var(--acento); text-transform: uppercase; letter-spacing: 0.12em; font-size: 0.74rem; font-weight: 900; }
.duas-colunas { display: grid; grid-template-columns: 1fr 1fr; gap: 22px; align-items: start; }
.colunas-2-1 { grid-template-columns: 2fr 1fr; }
.acoes { display: flex; gap: 10px; flex-wrap: wrap; align-items: center; }

.cartao { padding: 22px; background: var(--superficie); border: 1px solid var(--borda); border-radius: 20px; box-shadow: var(--sombra); margin-bottom: 22px; }
.cartao-topo { display: flex; justify-content: space-between; align-items: center; gap: 12px; margin-bottom: 12px; flex-wrap: wrap; }
.cartao-topo h2 { margin: 0; }
.cartao-erro { border-color: #f3c4c0; background: #fff7f6; }
.vazio { text-align: center; padding: 48px 24px; }
.texto-suave { color: var(--suave); }
.texto-erro { color: var(--erro); font-weight: 800; }
.contagem { color: var(--suave); font-weight: 700; margin: 0 0 12px; }
.nota { font-size: 0.86rem; margin-top: 14px; }

/* ---------- Alertas e badges ---------- */
.alerta { padding: 13px 16px; border-radius: 14px; margin-bottom: 18px; font-weight: 700; border: 1px solid transparent; }
.alerta-sucesso { background: #e8f6ef; color: var(--sucesso); border-color: #bfe5d1; }
.alerta-erro { background: #fdecea; color: var(--erro); border-color: #f3c4c0; }
.alerta-aviso { background: #fff7e0; color: var(--aviso); border-color: #f2dea3; }
.badge { display: inline-block; padding: 3px 10px; border-radius: 999px; font-size: 0.78rem; font-weight: 800; text-transform: uppercase; letter-spacing: 0.04em; vertical-align: middle; }
.badge-sucesso { background: #e8f6ef; color: var(--sucesso); }
.badge-aviso { background: #fff7e0; color: var(--aviso); }
.badge-erro { background: #fdecea; color: var(--erro); }
.badge-info { background: #e6edff; color: var(--info); }
.badge-neutro { background: var(--superficie-suave); color: var(--suave); }
.etiqueta { padding: 4px 9px; border-radius: 999px; background: var(--superficie-suave); color: var(--suave); font-size: 0.78rem; font-weight: 700; }

/* ---------- Botões ---------- */
.botao {
  display: inline-flex; align-items: center; justify-content: center; gap: 6px;
  border: 0; border-radius: 12px; padding: 11px 16px; background: var(--primaria); color: #fff;
  font-weight: 800; cursor: pointer; transition: background 0.15s, transform 0.15s; text-decoration: none;
}
.botao:hover { background: var(--primaria-clara); transform: translateY(-1px); text-decoration: none; }
.botao-secundario { background: var(--superficie-suave); color: var(--primaria); }
.botao-secundario:hover { background: #dfe7ef; }
.botao-pequeno { padding: 7px 11px; border-radius: 10px; font-size: 0.85rem; }
.botao-largo { width: 100%; margin-top: 10px; }
.botao-desativado { background: var(--borda); color: var(--suave); cursor: not-allowed; pointer-events: none; }
.botao-icone { width: 38px; height: 38px; border: 1px solid var(--borda); border-radius: 10px; background: #fff; color: var(--erro); font-size: 1.2rem; cursor: pointer; }
.botao-icone:disabled { opacity: 0.35; cursor: not-allowed; }

/* ---------- Formulários ---------- */
input, select, textarea { width: 100%; border: 1px solid var(--borda); border-radius: 11px; padding: 10px 12px; background: #fff; color: var(--texto); }
input:focus, select:focus, textarea:focus { outline: 2px solid rgba(23, 59, 104, 0.35); border-color: var(--primaria-clara); }
.formulario { display: grid; gap: 14px; }
.formulario label, .filtros label { display: grid; gap: 6px; color: var(--primaria); font-weight: 700; font-size: 0.9rem; }
.erro-campo { color: var(--erro); font-weight: 700; font-size: 0.85rem; }
.caixa-verificacao { display: flex !important; align-items: center; gap: 8px; }
.caixa-verificacao input { width: auto; }
.pesquisa-simples { display: flex; gap: 8px; min-width: 320px; }
.select-compacto { width: auto; padding: 7px 10px; font-size: 0.85rem; }

.filtros {
  display: grid; grid-template-columns: repeat(7, minmax(0, 1fr)); gap: 12px; align-items: end;
  padding: 18px; margin-bottom: 18px; background: var(--superficie); border: 1px solid var(--borda); border-radius: 20px;
}
.filtros-compactos { grid-template-columns: 200px auto; justify-content: start; }
.filtros-acoes { display: flex; gap: 8px; }

/* ---------- Tabelas ---------- */
.tabela { width: 100%; border-collapse: collapse; font-size: 0.93rem; }
.tabela th, .tabela td { padding: 10px 10px; border-bottom: 1px solid var(--borda); text-align: left; vertical-align: top; }
.tabela th { color: var(--suave); font-size: 0.78rem; text-transform: uppercase; letter-spacing: 0.06em; }
.tabela tfoot th { color: var(--primaria); font-size: 1rem; text-transform: none; border-bottom: 0; }
.tabela .num { text-align: right; white-space: nowrap; }
.tabela tr:last-child td { border-bottom: 0; }
.linha-inativa { opacity: 0.5; }

/* ---------- Painel ---------- */
.grelha-indicadores { display: grid; grid-template-columns: repeat(5, minmax(0, 1fr)); gap: 16px; margin-bottom: 22px; }
.indicador { display: grid; gap: 4px; padding: 18px; border-radius: 18px; background: var(--superficie); border: 1px solid var(--borda); box-shadow: var(--sombra); }
.indicador:hover { text-decoration: none; border-color: var(--primaria-clara); }
.indicador-valor { font-size: 1.7rem; font-weight: 900; color: var(--primaria); }
.indicador-nome { color: var(--suave); font-weight: 600; font-size: 0.85rem; }
.indicador-aviso .indicador-valor { color: var(--aviso); }
.indicador-erro .indicador-valor { color: var(--erro); }
.arquitetura-fluxo { display: flex; align-items: center; gap: 10px; flex-wrap: wrap; }
.bloco { display: grid; gap: 2px; padding: 12px 16px; border-radius: 14px; background: var(--superficie-suave); }
.bloco span { color: var(--suave); font-size: 0.82rem; }
.seta { color: var(--acento); font-weight: 900; white-space: nowrap; }

/* ---------- Catálogo de peças ---------- */
.grelha-pecas { display: grid; grid-template-columns: repeat(3, minmax(0, 1fr)); gap: 18px; }
.peca { display: grid; gap: 8px; padding: 18px; background: var(--superficie); border: 1px solid var(--borda); border-radius: 18px; box-shadow: var(--sombra); }
.peca header { display: flex; justify-content: space-between; align-items: center; }
.peca h3 { margin: 0; }
.peca .referencia { justify-self: start; }
.peca p { margin: 0; font-size: 0.9rem; }
.peca-stock-baixo { border-color: #f2dea3; background: #fffdf5; }
.peca-rodape { display: flex; justify-content: space-between; align-items: baseline; margin-top: 4px; }
.preco { color: var(--primaria); font-size: 1.25rem; }
.stock { color: var(--suave); font-size: 0.9rem; }
.stock em { color: var(--aviso); font-style: normal; font-weight: 800; }
.peca-acoes { display: flex; gap: 8px; flex-wrap: wrap; }

/* ---------- Formulário de venda ---------- */
.linha-peca { display: grid; grid-template-columns: 1fr 100px 38px; gap: 10px; align-items: end; padding: 12px 0; border-bottom: 1px dashed var(--borda); }
.linha-peca:last-child { border-bottom: 0; }
.linha-peca .info-peca, .linha-peca .erro-campo { grid-column: 1 / -1; font-size: 0.86rem; }
.opcoes-pagamento { display: grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap: 10px; margin-bottom: 12px; }
.opcao-pagamento { display: flex !important; gap: 10px; align-items: flex-start; padding: 12px; border: 1px solid var(--borda); border-radius: 14px; cursor: pointer; font-weight: 500 !important; }
.opcao-pagamento:has(input:checked) { border-color: var(--primaria); background: var(--superficie-suave); }
.opcao-pagamento input { width: auto; margin-top: 4px; }
.opcao-pagamento span { display: grid; gap: 2px; }
.opcao-pagamento small { color: var(--suave); font-weight: 500; }
.resumo-venda { position: sticky; top: 84px; }
.lista-resumo { list-style: none; padding: 0; margin: 0 0 12px; display: grid; gap: 6px; }
.lista-resumo li { display: flex; justify-content: space-between; gap: 10px; font-size: 0.92rem; }
.total-linha { display: flex; justify-content: space-between; align-items: baseline; padding: 12px 0; margin-bottom: 12px; border-top: 2px solid var(--primaria); font-size: 1.05rem; }
.total-linha strong { font-size: 1.5rem; color: var(--primaria); }

/* ---------- Encomenda a fornecedor ---------- */
.tabela-linhas-cabecalho, .linha-encomenda { display: grid; grid-template-columns: 130px 1fr 90px 130px 38px; gap: 8px; align-items: center; }
.tabela-linhas-cabecalho { color: var(--suave); font-size: 0.75rem; text-transform: uppercase; letter-spacing: 0.06em; padding: 0 0 6px; }
.linha-encomenda { padding: 6px 0; }
.linha-encomenda .linha-erro { grid-column: 1 / -1; }

/* ---------- Rodapé ---------- */
.rodape { display: flex; justify-content: space-between; gap: 16px; flex-wrap: wrap; padding: 26px 28px; background: var(--primaria); color: rgba(255, 255, 255, 0.8); font-size: 0.88rem; }
.rodape code { background: rgba(255, 255, 255, 0.12); color: #fff; }

/* ---------- Responsivo ---------- */
@media (max-width: 1000px) {
  .grelha-indicadores { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .grelha-pecas { grid-template-columns: repeat(2, minmax(0, 1fr)); }
  .filtros { grid-template-columns: repeat(3, minmax(0, 1fr)); }
  .duas-colunas, .colunas-2-1 { grid-template-columns: 1fr; }
  .resumo-venda { position: static; }
}
@media (max-width: 640px) {
  .grelha-indicadores, .grelha-pecas, .filtros, .opcoes-pagamento { grid-template-columns: 1fr; }
  .tabela-linhas-cabecalho { display: none; }
  .linha-encomenda { grid-template-columns: 1fr 1fr; }
  .conteudo { padding-top: 20px; }
  .tabela { display: block; overflow-x: auto; }
}

```

---

## php/public/clientes.php

```php
<?php
/**
 * Clientes: listagem com pesquisa e formulário para criar novo cliente.
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Repositorios\ClienteRepository;

$titulo = 'Clientes';
$erros = [];

try {
    $repo = new ClienteRepository();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $erros = $repo->validar($_POST);
        if (!$erros) {
            try {
                $id = $repo->criar($_POST);
                flash('sucesso', 'Cliente "' . trim($_POST['nome']) . '" criado com sucesso (nº ' . $id . ').');
                redirecionar('clientes.php');
            } catch (PDOException $e) {
                // 23000 = violação de chave única (NIF repetido)
                $erros['nif'] = $e->getCode() === '23000' ? 'Já existe um cliente com este NIF.' : 'Erro ao gravar: ' . $e->getMessage();
            }
        }
    }

    $pesquisa = trim($_GET['pesquisa'] ?? '');
    $lista = $repo->listar($pesquisa);
} catch (Throwable $excecao) {
    $titulo = 'Base de dados indisponível';
    require __DIR__ . '/../templates/erro.php';
    exit;
}

require __DIR__ . '/../templates/cabecalho.php';
?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Clientes</p>
    <h1>Base de clientes</h1>
    <p><?= count($lista) ?> cliente(s) registado(s) na BD1.</p>
  </div>
  <form class="pesquisa-simples" method="get" action="clientes.php">
    <input type="search" name="pesquisa" value="<?= e($pesquisa) ?>" placeholder="Nome, NIF ou email">
    <button class="botao botao-secundario" type="submit">Pesquisar</button>
  </form>
</section>

<div class="duas-colunas colunas-2-1">
  <section class="cartao">
    <table class="tabela">
      <thead><tr><th>Nome</th><th>NIF</th><th>Contacto</th><th class="num">Vendas</th><th class="num">Total gasto</th><th></th></tr></thead>
      <tbody>
        <?php if (!$lista): ?>
          <tr><td colspan="6" class="texto-suave">Nenhum cliente encontrado.</td></tr>
        <?php endif; ?>
        <?php foreach ($lista as $c): ?>
        <tr>
          <td><strong><?= e($c['nome']) ?></strong><br><small class="texto-suave"><?= e($c['morada']) ?></small></td>
          <td><?= e($c['nif']) ?></td>
          <td><?= e($c['email']) ?><br><small class="texto-suave"><?= e($c['telefone']) ?></small></td>
          <td class="num"><?= $c['num_vendas'] ?></td>
          <td class="num"><?= formatarPreco($c['total_gasto']) ?></td>
          <td><a class="botao botao-pequeno" href="venda.php?cliente_id=<?= $c['id'] ?>">Nova venda</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <section class="cartao">
    <h2>Novo cliente</h2>
    <form method="post" action="clientes.php" class="formulario" novalidate>
      <?= campoCsrf() ?>
      <label>Nome *
        <input type="text" name="nome" value="<?= antigo('nome') ?>" required>
        <?php if (isset($erros['nome'])): ?><span class="erro-campo"><?= e($erros['nome']) ?></span><?php endif; ?>
      </label>
      <label>NIF *
        <input type="text" name="nif" inputmode="numeric" maxlength="9" value="<?= antigo('nif') ?>" required>
        <?php if (isset($erros['nif'])): ?><span class="erro-campo"><?= e($erros['nif']) ?></span><?php endif; ?>
      </label>
      <label>Email *
        <input type="email" name="email" value="<?= antigo('email') ?>" required>
        <?php if (isset($erros['email'])): ?><span class="erro-campo"><?= e($erros['email']) ?></span><?php endif; ?>
      </label>
      <label>Telefone
        <input type="tel" name="telefone" value="<?= antigo('telefone') ?>">
      </label>
      <label>Morada
        <input type="text" name="morada" value="<?= antigo('morada') ?>">
      </label>
      <button class="botao" type="submit">Guardar cliente</button>
    </form>
  </section>
</div>
<?php require __DIR__ . '/../templates/rodape.php'; ?>

```

---

## php/public/encomenda_fornecedor.php

```php
<?php
/**
 * C) Submissão de encomenda a fornecedor
 * A lista de fornecedores vem da API Node.js (GET /api/fornecedores) e a
 * encomenda é submetida à mesma API (POST /api/encomendas), que a grava na BD2.
 * As peças a encomendar são escolhidas do catálogo da BD1 (para sugerir
 * referência, descrição e um preço de custo), mas podem ser editadas à mão.
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Api\ApiException;
use AutoLux\Repositorios\PecaRepository;

$titulo = 'Nova encomenda a fornecedor';
$erros = [];
$erroApi = null;

try {
    $pecas = (new PecaRepository())->listar();
} catch (Throwable $excecao) {
    $titulo = 'Base de dados indisponível';
    require __DIR__ . '/../templates/erro.php';
    exit;
}

try {
    $fornecedores = $apiFornecedores->listarFornecedores();
} catch (ApiException $e) {
    $fornecedores = [];
    $erroApi = $e->getMessage();
}

$fornecedorSelecionado = (int) ($_POST['fornecedor_id'] ?? $_GET['fornecedor_id'] ?? 0);
$observacoes = trim($_POST['observacoes'] ?? '');

// Linhas da encomenda (arrays paralelos vindos do formulário)
$linhas = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $n = count((array) ($_POST['referencia'] ?? []));
    for ($i = 0; $i < $n; $i++) {
        $linhas[] = [
            'referencia'     => trim($_POST['referencia'][$i] ?? ''),
            'descricao'      => trim($_POST['descricao'][$i] ?? ''),
            'quantidade'     => (int) ($_POST['quantidade'][$i] ?? 0),
            'preco_unitario' => str_replace(',', '.', trim($_POST['preco_unitario'][$i] ?? '')),
        ];
    }
} else {
    // Pré-preenche a partir de uma peça do catálogo (?peca_id=) — sugere repor até 2x o stock mínimo
    $pecaInicial = null;
    if (!empty($_GET['peca_id'])) {
        foreach ($pecas as $p) {
            if ((int) $p['id'] === (int) $_GET['peca_id']) {
                $pecaInicial = $p;
            }
        }
    }
    $linhas[] = $pecaInicial ? [
        'referencia'     => $pecaInicial['referencia'],
        'descricao'      => $pecaInicial['marca'] . ' ' . $pecaInicial['nome'],
        'quantidade'     => max(1, (int) $pecaInicial['stock_minimo'] * 2 - (int) $pecaInicial['stock']),
        'preco_unitario' => number_format((float) $pecaInicial['preco'] * 0.65, 2, '.', ''), // preço de custo estimado
    ] : ['referencia' => '', 'descricao' => '', 'quantidade' => 1, 'preco_unitario' => ''];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$erroApi) {
    if ($fornecedorSelecionado <= 0) {
        $erros['fornecedor_id'] = 'Selecione o fornecedor.';
    }
    $itens = [];
    foreach ($linhas as $i => $l) {
        if ($l['referencia'] === '' && $l['descricao'] === '' && $l['quantidade'] <= 0) {
            continue;
        }
        if ($l['referencia'] === '' || $l['descricao'] === '') {
            $erros["linha_$i"] = 'Referência e descrição são obrigatórias.';
        } elseif ($l['quantidade'] <= 0) {
            $erros["linha_$i"] = 'Quantidade deve ser pelo menos 1.';
        } elseif (!is_numeric($l['preco_unitario']) || (float) $l['preco_unitario'] < 0) {
            $erros["linha_$i"] = 'Preço unitário inválido.';
        } else {
            $itens[] = [
                'referencia_peca' => $l['referencia'],
                'descricao'       => $l['descricao'],
                'quantidade'      => $l['quantidade'],
                'preco_unitario'  => (float) $l['preco_unitario'],
            ];
        }
    }
    if (!$itens && !$erros) {
        $erros['itens'] = 'Adicione pelo menos uma linha à encomenda.';
    }

    if (!$erros) {
        try {
            $encomenda = $apiFornecedores->submeterEncomenda($fornecedorSelecionado, $itens, $observacoes ?: null, $utilizadorAtual['nome']);
            flash('sucesso', 'Encomenda nº ' . $encomenda['id'] . ' submetida ao fornecedor ' . $encomenda['fornecedor_nome'] .
                ' através da API Node.js. Total: ' . formatarPreco($encomenda['total']) . '.');
            redirecionar('encomendas_fornecedor.php?id=' . $encomenda['id']);
        } catch (ApiException $e) {
            $erros['itens'] = 'A API rejeitou a encomenda: ' . $e->getMessage();
        }
    }
}

$pecasJson = json_encode(array_map(fn($p) => [
    'id' => (int) $p['id'], 'referencia' => $p['referencia'], 'descricao' => $p['marca'] . ' ' . $p['nome'],
    'preco_custo' => round((float) $p['preco'] * 0.65, 2), 'stock' => (int) $p['stock'], 'stock_minimo' => (int) $p['stock_minimo'],
], $pecas), JSON_UNESCAPED_UNICODE);

require __DIR__ . '/../templates/cabecalho.php';
?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Compras</p>
    <h1>Encomenda a fornecedor</h1>
    <p>Submetida via <code>POST <?= e($config['API_BASE_URL']) ?>/encomendas</code> (Node.js → BD2).</p>
  </div>
</section>

<?php if ($erroApi): ?>
  <section class="cartao cartao-erro">
    <h2>API de fornecedores indisponível</h2>
    <p><?= e($erroApi) ?></p>
    <p class="texto-suave">Arranque a API com <code>npm run start:api</code> e recarregue a página.</p>
  </section>
<?php else: ?>
<?php if (isset($erros['itens'])): ?><div class="alerta alerta-erro"><?= e($erros['itens']) ?></div><?php endif; ?>

<form method="post" action="encomenda_fornecedor.php" class="formulario" id="form-encomenda" novalidate>
  <?= campoCsrf() ?>
  <div class="duas-colunas colunas-2-1">
    <div>
      <section class="cartao">
        <h2>1. Fornecedor</h2>
        <label>Fornecedor *
          <select name="fornecedor_id" required>
            <option value="">— Selecione o fornecedor —</option>
            <?php foreach ($fornecedores as $f): ?>
              <option value="<?= $f['id'] ?>" <?= $f['id'] == $fornecedorSelecionado ? 'selected' : '' ?>><?= e($f['nome']) ?> · entrega em <?= $f['prazo_entrega_dias'] ?> dias</option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($erros['fornecedor_id'])): ?><span class="erro-campo"><?= e($erros['fornecedor_id']) ?></span><?php endif; ?>
        </label>
      </section>

      <section class="cartao">
        <div class="cartao-topo">
          <h2>2. Peças a encomendar</h2>
          <div class="acoes">
            <select id="seletor-catalogo" class="select-compacto">
              <option value="">Adicionar do catálogo…</option>
              <?php foreach ($pecas as $p): ?>
                <option value="<?= $p['id'] ?>"><?= e($p['referencia']) ?> · <?= e($p['nome']) ?> (stock <?= $p['stock'] ?>/<?= $p['stock_minimo'] ?>)</option>
              <?php endforeach; ?>
            </select>
            <button type="button" class="botao botao-pequeno botao-secundario" id="adicionar-linha-vazia">+ Linha manual</button>
          </div>
        </div>

        <div class="tabela-linhas">
          <div class="tabela-linhas-cabecalho"><span>Referência</span><span>Descrição</span><span>Qtd.</span><span>Preço unit. (€)</span><span></span></div>
          <div id="linhas-encomenda">
            <?php foreach ($linhas as $i => $l): ?>
            <div class="linha-encomenda">
              <input type="text" name="referencia[]" value="<?= e($l['referencia']) ?>" placeholder="REF-000" required>
              <input type="text" name="descricao[]" value="<?= e($l['descricao']) ?>" placeholder="Descrição da peça" required>
              <input type="number" name="quantidade[]" class="input-quantidade" min="1" value="<?= $l['quantidade'] ?: 1 ?>" required>
              <input type="number" name="preco_unitario[]" class="input-preco" min="0" step="0.01" value="<?= e($l['preco_unitario']) ?>" placeholder="0.00" required>
              <button type="button" class="botao-icone remover-linha" aria-label="Remover linha">×</button>
              <?php if (isset($erros["linha_$i"])): ?><span class="erro-campo linha-erro"><?= e($erros["linha_$i"]) ?></span><?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <template id="template-linha-encomenda">
          <div class="linha-encomenda">
            <input type="text" name="referencia[]" placeholder="REF-000" required>
            <input type="text" name="descricao[]" placeholder="Descrição da peça" required>
            <input type="number" name="quantidade[]" class="input-quantidade" min="1" value="1" required>
            <input type="number" name="preco_unitario[]" class="input-preco" min="0" step="0.01" placeholder="0.00" required>
            <button type="button" class="botao-icone remover-linha" aria-label="Remover linha">×</button>
          </div>
        </template>
      </section>
    </div>

    <aside class="cartao resumo-venda">
      <h2>Resumo</h2>
      <div class="total-linha"><span>Total estimado</span><strong id="resumo-total">0,00 €</strong></div>
      <label>Observações
        <textarea name="observacoes" rows="3" maxlength="255" placeholder="Ex.: urgente, entregar de manhã"><?= e($observacoes) ?></textarea>
      </label>
      <button class="botao botao-largo" type="submit">Submeter encomenda</button>
      <a class="botao botao-secundario botao-largo" href="encomendas_fornecedor.php">Ver encomendas</a>
    </aside>
  </div>
</form>

<script id="dados-pecas" type="application/json"><?= $pecasJson ?></script>
<?php endif; ?>
<?php require __DIR__ . '/../templates/rodape.php'; ?>

```

---

## php/public/encomendas_fornecedor.php

```php
<?php
/**
 * Lista de encomendas a fornecedores (GET /api/encomendas) com filtro por
 * estado/fornecedor, detalhe de uma encomenda (?id=) e alteração de estado
 * (PATCH /api/encomendas/:id/estado). Tudo através da API Node.js.
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Api\ApiException;

$titulo = 'Encomendas a fornecedores';
$erroApi = null;
$estados = ['pendente', 'enviada', 'recebida', 'cancelada'];

// Alteração de estado (formulário POST na página de detalhe)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'estado') {
    try {
        $enc = $apiFornecedores->alterarEstado((int) $_POST['id'], $_POST['estado'] ?? '');
        flash('sucesso', 'Encomenda nº ' . $enc['id'] . ' passou ao estado "' . $enc['estado'] . '".');
    } catch (ApiException $e) {
        flash('erro', $e->getMessage());
    }
    redirecionar('encomendas_fornecedor.php?id=' . (int) $_POST['id']);
}

$id = (int) ($_GET['id'] ?? 0);
$filtroEstado = $_GET['estado'] ?? '';
$filtroFornecedor = $_GET['fornecedor_id'] ?? '';
$detalhe = null;
$lista = [];

try {
    if ($id > 0) {
        $detalhe = $apiFornecedores->obterEncomenda($id);
    } else {
        $lista = $apiFornecedores->listarEncomendas(['estado' => $filtroEstado, 'fornecedor_id' => $filtroFornecedor]);
    }
} catch (ApiException $e) {
    $erroApi = $e->getMessage();
}

require __DIR__ . '/../templates/cabecalho.php';
?>
<?php if ($erroApi): ?>
  <section class="cartao cartao-erro">
    <h1>API de fornecedores indisponível</h1>
    <p><?= e($erroApi) ?></p>
    <a class="botao botao-secundario" href="encomendas_fornecedor.php">Voltar à lista</a>
  </section>

<?php elseif ($detalhe): ?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Compras</p>
    <h1>Encomenda nº <?= $detalhe['id'] ?> <?= badgeEstado($detalhe['estado']) ?></h1>
    <p><?= e($detalhe['fornecedor_nome']) ?> · <?= formatarData($detalhe['data_encomenda']) ?><?= !empty($detalhe['criado_por']) ? ' · submetida por ' . e($detalhe['criado_por']) : '' ?></p>
  </div>
  <a class="botao botao-secundario" href="encomendas_fornecedor.php">Todas as encomendas</a>
</section>

<div class="duas-colunas colunas-2-1">
  <section class="cartao">
    <h2>Linhas</h2>
    <table class="tabela">
      <thead><tr><th>Referência</th><th>Descrição</th><th class="num">Qtd.</th><th class="num">Preço unit.</th><th class="num">Subtotal</th></tr></thead>
      <tbody>
        <?php foreach ($detalhe['itens'] as $i): ?>
        <tr>
          <td><code><?= e($i['referencia_peca']) ?></code></td>
          <td><?= e($i['descricao']) ?></td>
          <td class="num"><?= $i['quantidade'] ?></td>
          <td class="num"><?= formatarPreco($i['preco_unitario']) ?></td>
          <td class="num"><?= formatarPreco($i['subtotal']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot><tr><th colspan="4" class="num">Total</th><th class="num"><?= formatarPreco($detalhe['total']) ?></th></tr></tfoot>
    </table>
    <?php if ($detalhe['observacoes']): ?><p><em><?= e($detalhe['observacoes']) ?></em></p><?php endif; ?>
  </section>

  <aside class="cartao">
    <h2>Alterar estado</h2>
    <p class="texto-suave">Envia <code>PATCH /api/encomendas/<?= $detalhe['id'] ?>/estado</code>.</p>
    <form method="post" action="encomendas_fornecedor.php" class="formulario">
      <?= campoCsrf() ?>
      <input type="hidden" name="acao" value="estado">
      <input type="hidden" name="id" value="<?= $detalhe['id'] ?>">
      <label>Novo estado
        <select name="estado">
          <?php foreach ($estados as $est): ?>
            <option value="<?= $est ?>" <?= $est === $detalhe['estado'] ? 'selected' : '' ?>><?= ucfirst($est) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <button class="botao botao-largo" type="submit">Guardar estado</button>
    </form>
  </aside>
</div>

<?php else: ?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Compras</p>
    <h1>Encomendas a fornecedores</h1>
    <p>Obtidas via <code>GET <?= e($config['API_BASE_URL']) ?>/encomendas</code> (Node.js → BD2).</p>
  </div>
  <a href="encomenda_fornecedor.php" class="botao">+ Nova encomenda</a>
</section>

<form class="filtros filtros-compactos" method="get" action="encomendas_fornecedor.php">
  <label>Estado
    <select name="estado">
      <option value="">Todos</option>
      <?php foreach ($estados as $est): ?>
        <option value="<?= $est ?>" <?= $est === $filtroEstado ? 'selected' : '' ?>><?= ucfirst($est) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <?php if ($filtroFornecedor !== ''): ?><input type="hidden" name="fornecedor_id" value="<?= e($filtroFornecedor) ?>"><?php endif; ?>
  <div class="filtros-acoes">
    <button class="botao" type="submit">Filtrar</button>
    <?php if ($filtroEstado !== '' || $filtroFornecedor !== ''): ?><a class="botao botao-secundario" href="encomendas_fornecedor.php">Limpar</a><?php endif; ?>
  </div>
</form>

<section class="cartao">
  <table class="tabela">
    <thead><tr><th>#</th><th>Data</th><th>Fornecedor</th><th>Submetida por</th><th>Estado</th><th class="num">Linhas</th><th class="num">Unidades</th><th class="num">Total</th><th></th></tr></thead>
    <tbody>
      <?php if (!$lista): ?>
        <tr><td colspan="9" class="texto-suave">Nenhuma encomenda encontrada.</td></tr>
      <?php endif; ?>
      <?php foreach ($lista as $enc): ?>
      <tr>
        <td>#<?= $enc['id'] ?></td>
        <td><?= formatarData($enc['data_encomenda']) ?></td>
        <td><?= e($enc['fornecedor_nome']) ?></td>
        <td><?= e($enc['criado_por'] ?? '—') ?></td>
        <td><?= badgeEstado($enc['estado']) ?></td>
        <td class="num"><?= $enc['num_linhas'] ?></td>
        <td class="num"><?= $enc['total_unidades'] ?></td>
        <td class="num"><strong><?= formatarPreco($enc['total']) ?></strong></td>
        <td><a class="botao botao-pequeno botao-secundario" href="encomendas_fornecedor.php?id=<?= $enc['id'] ?>">Detalhe</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>
<?php endif; ?>
<?php require __DIR__ . '/../templates/rodape.php'; ?>

```

---

## php/public/fornecedores.php

```php
<?php
/**
 * C) Lista de fornecedores
 * Os dados NÃO vêm da BD1: são pedidos por HTTP à Web API Node.js
 * (GET /api/fornecedores), que por sua vez lê a BD2. Também permite criar
 * um fornecedor (POST /api/fornecedores).
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Api\ApiException;

$titulo = 'Fornecedores';
$erroApi = null;
$erros = [];
$lista = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $novo = $apiFornecedores->criarFornecedor([
            'nome'               => trim($_POST['nome'] ?? ''),
            'nif'                => trim($_POST['nif'] ?? ''),
            'email'              => trim($_POST['email'] ?? ''),
            'telefone'           => trim($_POST['telefone'] ?? ''),
            'morada'             => trim($_POST['morada'] ?? ''),
            'prazo_entrega_dias' => (int) ($_POST['prazo_entrega_dias'] ?? 5),
        ]);
        flash('sucesso', 'Fornecedor "' . $novo['nome'] . '" criado através da API Node.js (id ' . $novo['id'] . ').');
        redirecionar('fornecedores.php');
    } catch (ApiException $e) {
        $erros['geral'] = $e->getMessage(); // a API devolve as mensagens de validação
    }
}

try {
    $lista = $apiFornecedores->listarFornecedores(isset($_GET['todos']));
} catch (ApiException $e) {
    $erroApi = $e->getMessage();
}

require __DIR__ . '/../templates/cabecalho.php';
?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Compras</p>
    <h1>Fornecedores</h1>
    <p>Obtidos via <code>GET <?= e($config['API_BASE_URL']) ?>/fornecedores</code> (Node.js → BD2).</p>
  </div>
  <a href="encomenda_fornecedor.php" class="botao">+ Nova encomenda</a>
</section>

<?php if ($erroApi): ?>
  <section class="cartao cartao-erro">
    <h2>API de fornecedores indisponível</h2>
    <p><?= e($erroApi) ?></p>
    <p class="texto-suave">Arranque a API com <code>npm run start:api</code> (ou <code>npm start</code> para arrancar tudo) e recarregue a página.</p>
  </section>
<?php else: ?>
<div class="duas-colunas colunas-2-1">
  <section class="cartao">
    <div class="cartao-topo">
      <h2><?= count($lista) ?> fornecedor(es)</h2>
      <a href="fornecedores.php<?= isset($_GET['todos']) ? '' : '?todos=1' ?>"><?= isset($_GET['todos']) ? 'Só ativos' : 'Incluir inativos' ?></a>
    </div>
    <table class="tabela">
      <thead><tr><th>Nome</th><th>NIF</th><th>Contacto</th><th class="num">Prazo</th><th class="num">Encomendas</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($lista as $f): ?>
        <tr class="<?= $f['ativo'] ? '' : 'linha-inativa' ?>">
          <td><strong><?= e($f['nome']) ?></strong><br><small class="texto-suave"><?= e($f['morada']) ?></small></td>
          <td><?= e($f['nif']) ?></td>
          <td><?= e($f['email']) ?><br><small class="texto-suave"><?= e($f['telefone']) ?></small></td>
          <td class="num"><?= $f['prazo_entrega_dias'] ?> dias</td>
          <td class="num"><a href="encomendas_fornecedor.php?fornecedor_id=<?= $f['id'] ?>"><?= $f['total_encomendas'] ?></a></td>
          <td><a class="botao botao-pequeno" href="encomenda_fornecedor.php?fornecedor_id=<?= $f['id'] ?>">Encomendar</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <section class="cartao">
    <h2>Novo fornecedor</h2>
    <?php if (isset($erros['geral'])): ?><div class="alerta alerta-erro"><?= e($erros['geral']) ?></div><?php endif; ?>
    <form method="post" action="fornecedores.php" class="formulario" novalidate>
      <?= campoCsrf() ?>
      <label>Nome *<input type="text" name="nome" value="<?= antigo('nome') ?>" required></label>
      <label>NIF *<input type="text" name="nif" maxlength="9" inputmode="numeric" value="<?= antigo('nif') ?>" required></label>
      <label>Email *<input type="email" name="email" value="<?= antigo('email') ?>" required></label>
      <label>Telefone<input type="tel" name="telefone" value="<?= antigo('telefone') ?>"></label>
      <label>Morada<input type="text" name="morada" value="<?= antigo('morada') ?>"></label>
      <label>Prazo de entrega (dias)<input type="number" name="prazo_entrega_dias" min="1" value="<?= antigo('prazo_entrega_dias', 5) ?>"></label>
      <button class="botao" type="submit">Guardar via API</button>
    </form>
  </section>
</div>
<?php endif; ?>
<?php require __DIR__ . '/../templates/rodape.php'; ?>

```

---

## php/public/funcionarios.php

```php
<?php
/**
 * Gestão de funcionários (apenas administradores):
 * listar, criar, ativar/desativar e redefinir password.
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Auth;
use AutoLux\Repositorios\FuncionarioRepository;

Auth::exigirAdmin();

$titulo = 'Funcionários';
$erros = [];

try {
    $repo = new FuncionarioRepository();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $acao = $_POST['acao'] ?? 'criar';
        $id = (int) ($_POST['id'] ?? 0);

        if ($acao === 'criar') {
            $erros = $repo->validar($_POST);
            if (!$erros) {
                try {
                    $repo->criar($_POST);
                    flash('sucesso', 'Funcionário "' . trim($_POST['nome']) . '" criado. Já pode iniciar sessão com o utilizador "' . strtolower(trim($_POST['utilizador'])) . '".');
                    redirecionar('funcionarios.php');
                } catch (PDOException $e) {
                    $erros['utilizador'] = $e->getCode() === '23000' ? 'Já existe um funcionário com esse utilizador.' : $e->getMessage();
                }
            }
        } elseif ($acao === 'ativar' || $acao === 'desativar') {
            $alvo = $repo->obter($id);
            if (!$alvo) {
                flash('erro', 'Funcionário não encontrado.');
            } elseif ($acao === 'desativar' && $id === (int) $utilizadorAtual['id']) {
                flash('erro', 'Não pode desativar a sua própria conta.');
            } elseif ($acao === 'desativar' && $alvo['perfil'] === 'admin' && $repo->contarAdminsAtivos() <= 1) {
                flash('erro', 'Tem de existir pelo menos um administrador ativo.');
            } else {
                $repo->alterarAtivo($id, $acao === 'ativar');
                flash('sucesso', 'Conta de ' . $alvo['nome'] . ($acao === 'ativar' ? ' ativada.' : ' desativada.'));
            }
            redirecionar('funcionarios.php');
        } elseif ($acao === 'password') {
            $alvo = $repo->obter($id);
            $erroPw = FuncionarioRepository::validarPassword($_POST['password'] ?? '');
            if (!$alvo) {
                flash('erro', 'Funcionário não encontrado.');
            } elseif ($erroPw) {
                flash('erro', $erroPw);
            } else {
                $repo->alterarPassword($id, $_POST['password']);
                flash('sucesso', 'Password de ' . $alvo['nome'] . ' redefinida.');
            }
            redirecionar('funcionarios.php');
        }
    }

    $lista = $repo->listar();
} catch (Throwable $excecao) {
    $titulo = 'Base de dados indisponível';
    require __DIR__ . '/../templates/erro.php';
    exit;
}

require __DIR__ . '/../templates/cabecalho.php';
?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Administração</p>
    <h1>Funcionários</h1>
    <p><?= count($lista) ?> conta(s). As passwords são guardadas como hash bcrypt (<code>password_hash</code>).</p>
  </div>
</section>

<div class="duas-colunas colunas-2-1">
  <section class="cartao">
    <table class="tabela">
      <thead><tr><th>Nome</th><th>Utilizador</th><th>Perfil</th><th>Estado</th><th>Último login</th><th class="num">Vendas</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($lista as $f): ?>
        <tr class="<?= $f['ativo'] ? '' : 'linha-inativa' ?>">
          <td><strong><?= e($f['nome']) ?></strong><?= $f['id'] == $utilizadorAtual['id'] ? ' <span class="etiqueta">você</span>' : '' ?><br><small class="texto-suave"><?= e($f['email']) ?></small></td>
          <td><code><?= e($f['utilizador']) ?></code></td>
          <td><?= e(FuncionarioRepository::PERFIS[$f['perfil']]) ?></td>
          <td><?= $f['ativo'] ? '<span class="badge badge-sucesso">ativo</span>' : '<span class="badge badge-erro">inativo</span>' ?></td>
          <td><?= formatarData($f['ultimo_login']) ?></td>
          <td class="num"><?= $f['num_vendas'] ?></td>
          <td>
            <div class="acoes acoes-inline">
              <form method="post" action="funcionarios.php">
                <?= campoCsrf() ?>
                <input type="hidden" name="id" value="<?= $f['id'] ?>">
                <input type="hidden" name="acao" value="<?= $f['ativo'] ? 'desativar' : 'ativar' ?>">
                <button class="botao botao-pequeno botao-secundario" type="submit" <?= $f['id'] == $utilizadorAtual['id'] ? 'disabled title="Não pode desativar a sua conta"' : '' ?>><?= $f['ativo'] ? 'Desativar' : 'Ativar' ?></button>
              </form>
              <details class="popover">
                <summary class="botao botao-pequeno botao-secundario">Nova password</summary>
                <form method="post" action="funcionarios.php" class="formulario popover-conteudo">
                  <?= campoCsrf() ?>
                  <input type="hidden" name="id" value="<?= $f['id'] ?>">
                  <input type="hidden" name="acao" value="password">
                  <label>Nova password para <?= e($f['utilizador']) ?>
                    <input type="password" name="password" minlength="6" autocomplete="new-password" required>
                  </label>
                  <button class="botao botao-pequeno" type="submit">Guardar</button>
                </form>
              </details>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <section class="cartao">
    <h2>Novo funcionário</h2>
    <form method="post" action="funcionarios.php" class="formulario" novalidate>
      <?= campoCsrf() ?>
      <input type="hidden" name="acao" value="criar">
      <label>Nome *
        <input type="text" name="nome" value="<?= antigo('nome') ?>" required>
        <?php if (isset($erros['nome'])): ?><span class="erro-campo"><?= e($erros['nome']) ?></span><?php endif; ?>
      </label>
      <label>Utilizador *
        <input type="text" name="utilizador" value="<?= antigo('utilizador') ?>" autocomplete="off" required>
        <?php if (isset($erros['utilizador'])): ?><span class="erro-campo"><?= e($erros['utilizador']) ?></span><?php endif; ?>
      </label>
      <label>Email *
        <input type="email" name="email" value="<?= antigo('email') ?>" required>
        <?php if (isset($erros['email'])): ?><span class="erro-campo"><?= e($erros['email']) ?></span><?php endif; ?>
      </label>
      <label>Password * <small class="texto-suave">(mín. 6 caracteres)</small>
        <input type="password" name="password" autocomplete="new-password" required>
        <?php if (isset($erros['password'])): ?><span class="erro-campo"><?= e($erros['password']) ?></span><?php endif; ?>
      </label>
      <label>Perfil *
        <select name="perfil">
          <?php foreach (FuncionarioRepository::PERFIS as $codigo => $nome): ?>
            <option value="<?= $codigo ?>" <?= ($_POST['perfil'] ?? 'funcionario') === $codigo ? 'selected' : '' ?>><?= e($nome) ?></option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($erros['perfil'])): ?><span class="erro-campo"><?= e($erros['perfil']) ?></span><?php endif; ?>
      </label>
      <button class="botao" type="submit">Criar funcionário</button>
    </form>
  </section>
</div>
<?php require __DIR__ . '/../templates/rodape.php'; ?>

```

---

## php/public/index.php

```php
<?php
/**
 * Painel inicial: indicadores rápidos das duas bases de dados
 * (BD1 via PDO, BD2 via API Node.js) e atalhos para as funcionalidades.
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Repositorios\PecaRepository;
use AutoLux\Repositorios\ClienteRepository;
use AutoLux\Repositorios\VendaRepository;

$titulo = 'Painel';

try {
    $pecas = new PecaRepository();
    $numPecas = count($pecas->listar());
    $stockBaixo = $pecas->listar(['so_stock_baixo' => '1']);
    $numClientes = count((new ClienteRepository())->paraDropdown());
    $resumoVendas = (new VendaRepository())->resumo();
    $ultimasVendas = (new VendaRepository())->listar(5);
} catch (Throwable $excecao) {
    $titulo = 'Base de dados indisponível';
    require __DIR__ . '/../templates/erro.php';
    exit;
}

// A API pode estar em baixo sem impedir o resto da página de funcionar
$apiOk = $apiFornecedores->saudavel();
$encomendasPendentes = [];
if ($apiOk) {
    try {
        $encomendasPendentes = $apiFornecedores->listarEncomendas(['estado' => 'pendente']);
    } catch (Throwable) {
        $apiOk = false;
    }
}

require __DIR__ . '/../templates/cabecalho.php';
?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Gestão de armazém</p>
    <h1>Bem-vindo à AutoLux</h1>
    <p>Consulte stock e clientes, registe vendas e encomende peças aos fornecedores.</p>
  </div>
  <a href="venda.php" class="botao">+ Nova venda</a>
</section>

<section class="grelha-indicadores">
  <a class="indicador" href="pecas.php">
    <span class="indicador-valor"><?= $numPecas ?></span>
    <span class="indicador-nome">Peças em catálogo</span>
  </a>
  <a class="indicador <?= $stockBaixo ? 'indicador-aviso' : '' ?>" href="pecas.php?so_stock_baixo=1">
    <span class="indicador-valor"><?= count($stockBaixo) ?></span>
    <span class="indicador-nome">Peças com stock baixo</span>
  </a>
  <a class="indicador" href="clientes.php">
    <span class="indicador-valor"><?= $numClientes ?></span>
    <span class="indicador-nome">Clientes</span>
  </a>
  <a class="indicador" href="vendas.php">
    <span class="indicador-valor"><?= formatarPreco($resumoVendas['faturacao']) ?></span>
    <span class="indicador-nome"><?= $resumoVendas['num_vendas'] ?> vendas · hoje <?= formatarPreco($resumoVendas['faturacao_hoje']) ?></span>
  </a>
  <a class="indicador <?= $apiOk ? '' : 'indicador-erro' ?>" href="encomendas_fornecedor.php">
    <span class="indicador-valor"><?= $apiOk ? count($encomendasPendentes) : '—' ?></span>
    <span class="indicador-nome"><?= $apiOk ? 'Encomendas pendentes (API Node.js)' : 'API Node.js indisponível' ?></span>
  </a>
</section>

<div class="duas-colunas">
  <section class="cartao">
    <div class="cartao-topo">
      <h2>Últimas vendas</h2>
      <a href="vendas.php">Ver todas</a>
    </div>
    <?php if (!$ultimasVendas): ?>
      <p class="texto-suave">Ainda não há vendas registadas.</p>
    <?php else: ?>
    <table class="tabela">
      <thead><tr><th>#</th><th>Cliente</th><th>Data</th><th>Pagamento</th><th class="num">Total</th></tr></thead>
      <tbody>
        <?php foreach ($ultimasVendas as $v): ?>
        <tr>
          <td><a href="venda_detalhe.php?id=<?= $v['id'] ?>">#<?= $v['id'] ?></a></td>
          <td><?= e($v['cliente']) ?></td>
          <td><?= formatarData($v['data_venda']) ?></td>
          <td><?= e($pagamentos->nomePorCodigo($v['tipo_pagamento'])) ?></td>
          <td class="num"><?= formatarPreco($v['total']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </section>

  <section class="cartao">
    <div class="cartao-topo">
      <h2>Stock abaixo do mínimo</h2>
      <a href="encomenda_fornecedor.php">Encomendar</a>
    </div>
    <?php if (!$stockBaixo): ?>
      <p class="texto-suave">Todas as peças estão acima do stock mínimo.</p>
    <?php else: ?>
    <table class="tabela">
      <thead><tr><th>Referência</th><th>Peça</th><th class="num">Stock</th><th class="num">Mínimo</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($stockBaixo as $p): ?>
        <tr>
          <td><code><?= e($p['referencia']) ?></code></td>
          <td><?= e($p['marca']) ?> <?= e($p['nome']) ?></td>
          <td class="num texto-erro"><?= $p['stock'] ?></td>
          <td class="num"><?= $p['stock_minimo'] ?></td>
          <td><a class="botao botao-pequeno botao-secundario" href="encomenda_fornecedor.php?peca_id=<?= $p['id'] ?>">Encomendar</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </section>
</div>

<section class="cartao arquitetura">
  <h2>Como está montado</h2>
  <div class="arquitetura-fluxo">
    <div class="bloco"><strong>Interface PHP</strong><span>páginas dinâmicas (esta app)</span></div>
    <div class="seta">PDO →</div>
    <div class="bloco"><strong>BD1 MySQL</strong><span>clientes · peças · vendas</span></div>
    <div class="seta">cURL/HTTP →</div>
    <div class="bloco"><strong>API Node.js</strong><span>Express · /api/fornecedores · /api/encomendas</span></div>
    <div class="seta">mysql2 →</div>
    <div class="bloco"><strong>BD2 MySQL</strong><span>fornecedores · encomendas</span></div>
  </div>
</section>
<?php require __DIR__ . '/../templates/rodape.php'; ?>

```

---

## php/public/login.php

```php
<?php
/**
 * Página de autenticação de funcionários.
 * É a única página pública (ver $paginasPublicas em src/bootstrap.php).
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Auth;

$titulo = 'Iniciar sessão';
$erro = null;

// Só aceita redirecionar para páginas internas (evita "open redirect" para sites externos)
$redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? 'index.php';
if (!preg_match('/^[a-z_]+\.php(\?[^\s]*)?$/i', $redirect)) {
    $redirect = 'index.php';
}

if (Auth::autenticado()) {
    redirecionar($redirect);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $erro = Auth::entrar($_POST['utilizador'] ?? '', $_POST['password'] ?? '');
        if ($erro === null) {
            flash('sucesso', 'Bem-vindo(a), ' . Auth::utilizador()['nome'] . '.');
            redirecionar($redirect);
        }
    } catch (Throwable $excecao) {
        $titulo = 'Base de dados indisponível';
        require __DIR__ . '/../templates/erro.php';
        exit;
    }
}
?>
<!doctype html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($titulo) ?> | AutoLux Gestão</title>
  <link rel="stylesheet" href="assets/estilos.css">
</head>
<body class="pagina-login">
<main class="cartao-login">
  <a class="marca" href="login.php"><span class="marca-simbolo">AL</span> AutoLux <small>Armazém de peças</small></a>
  <p class="rotulo">Área reservada a funcionários</p>
  <h1>Iniciar sessão</h1>

  <?php foreach (obterFlash() as $f): ?>
    <div class="alerta alerta-<?= e($f['tipo']) ?>"><?= e($f['mensagem']) ?></div>
  <?php endforeach; ?>
  <?php if ($erro): ?><div class="alerta alerta-erro"><?= e($erro) ?></div><?php endif; ?>

  <form method="post" action="login.php" class="formulario" novalidate>
    <?= campoCsrf() ?>
    <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
    <label>Utilizador
      <input type="text" name="utilizador" value="<?= antigo('utilizador') ?>" autocomplete="username" autofocus required>
    </label>
    <label>Password
      <input type="password" name="password" autocomplete="current-password" required>
    </label>
    <button class="botao botao-largo" type="submit">Entrar</button>
  </form>

  <details class="credenciais-teste">
    <summary>Credenciais de teste</summary>
    <ul>
      <li><code>admin</code> / <code>admin123</code> — administrador (gere funcionários)</li>
      <li><code>ana</code> / <code>ana123</code> — funcionária</li>
    </ul>
  </details>
</main>
</body>
</html>

```

---

## php/public/logout.php

```php
<?php
/**
 * Termina a sessão do funcionário. Aceita apenas POST (com token CSRF) para
 * que um simples link malicioso não consiga fazer logout ao utilizador.
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Auth;

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    redirecionar('index.php');
}

Auth::sair();
flash('sucesso', 'Sessão terminada. Até breve!');
redirecionar('login.php');

```

---

## php/public/pecas.php

```php
<?php
/**
 * A) Catálogo de peças
 * Lista as peças da BD1 com filtros por marca, tipo de peça e gama de preço
 * (mais pesquisa por texto e "só stock baixo"). Os filtros vêm por GET para
 * o URL poder ser partilhado/guardado.
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Repositorios\PecaRepository;

$titulo = 'Catálogo de peças';

$filtros = [
    'pesquisa'       => trim($_GET['pesquisa'] ?? ''),
    'marca_id'       => $_GET['marca_id'] ?? '',
    'tipo_id'        => $_GET['tipo_id'] ?? '',
    'preco_min'      => $_GET['preco_min'] ?? '',
    'preco_max'      => $_GET['preco_max'] ?? '',
    'so_stock_baixo' => $_GET['so_stock_baixo'] ?? '',
];
// Gamas de preço pré-definidas (dropdown) para além dos campos livres
$gamas = [
    ''        => 'Qualquer preço',
    '0-25'    => 'Até 25 €',
    '25-50'   => '25 € a 50 €',
    '50-100'  => '50 € a 100 €',
    '100-999999' => 'Mais de 100 €',
];
$gamaSelecionada = $_GET['gama'] ?? '';
if ($gamaSelecionada !== '' && isset($gamas[$gamaSelecionada])) {
    [$filtros['preco_min'], $filtros['preco_max']] = explode('-', $gamaSelecionada);
}
$haFiltros = array_filter($filtros, fn($v) => $v !== '');

try {
    $repo = new PecaRepository();
    $lista = $repo->listar($filtros);
    $marcas = $repo->marcas();
    $tipos = $repo->tiposPeca();
    $intervalo = $repo->intervaloPrecos();
} catch (Throwable $excecao) {
    $titulo = 'Base de dados indisponível';
    require __DIR__ . '/../templates/erro.php';
    exit;
}

require __DIR__ . '/../templates/cabecalho.php';
?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Catálogo</p>
    <h1>Peças em stock</h1>
    <p>Dados obtidos da base de dados <code><?= e($config['DB1_NAME']) ?></code> (tabela <code>pecas</code>).</p>
  </div>
</section>

<form class="filtros" method="get" action="pecas.php">
  <label>Pesquisa
    <input type="search" name="pesquisa" value="<?= e($filtros['pesquisa']) ?>" placeholder="Nome, referência ou descrição">
  </label>
  <label>Marca
    <select name="marca_id">
      <option value="">Todas</option>
      <?php foreach ($marcas as $m): ?>
        <option value="<?= $m['id'] ?>" <?= (string) $m['id'] === (string) $filtros['marca_id'] ? 'selected' : '' ?>><?= e($m['nome']) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Tipo de peça
    <select name="tipo_id">
      <option value="">Todos</option>
      <?php foreach ($tipos as $t): ?>
        <option value="<?= $t['id'] ?>" <?= (string) $t['id'] === (string) $filtros['tipo_id'] ? 'selected' : '' ?>><?= e($t['nome']) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Gama de preço
    <select name="gama">
      <?php foreach ($gamas as $valor => $rotulo): ?>
        <option value="<?= $valor ?>" <?= $valor === $gamaSelecionada ? 'selected' : '' ?>><?= $rotulo ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Preço mín. (€)
    <input type="number" step="0.01" min="0" name="preco_min" value="<?= e($_GET['preco_min'] ?? '') ?>" placeholder="<?= e($intervalo['minimo']) ?>">
  </label>
  <label>Preço máx. (€)
    <input type="number" step="0.01" min="0" name="preco_max" value="<?= e($_GET['preco_max'] ?? '') ?>" placeholder="<?= e($intervalo['maximo']) ?>">
  </label>
  <label class="caixa-verificacao">
    <input type="checkbox" name="so_stock_baixo" value="1" <?= $filtros['so_stock_baixo'] ? 'checked' : '' ?>> Só stock baixo
  </label>
  <div class="filtros-acoes">
    <button class="botao" type="submit">Filtrar</button>
    <?php if ($haFiltros): ?><a class="botao botao-secundario" href="pecas.php">Limpar</a><?php endif; ?>
  </div>
</form>

<p class="contagem"><?= count($lista) ?> peça(s) encontrada(s)</p>

<?php if (!$lista): ?>
  <section class="cartao vazio">
    <h2>Sem resultados</h2>
    <p>Altere ou limpe os filtros para ver mais peças.</p>
  </section>
<?php else: ?>
<div class="grelha-pecas">
  <?php foreach ($lista as $p): ?>
    <article class="peca <?= $p['stock'] <= $p['stock_minimo'] ? 'peca-stock-baixo' : '' ?>">
      <header>
        <span class="rotulo"><?= e($p['marca']) ?></span>
        <span class="etiqueta"><?= e($p['tipo']) ?></span>
      </header>
      <h3><?= e($p['nome']) ?></h3>
      <code class="referencia"><?= e($p['referencia']) ?></code>
      <p class="texto-suave"><?= e($p['descricao']) ?></p>
      <div class="peca-rodape">
        <strong class="preco"><?= formatarPreco($p['preco']) ?></strong>
        <span class="stock">Stock: <b><?= $p['stock'] ?></b><?= $p['stock'] <= $p['stock_minimo'] ? ' <em>(baixo)</em>' : '' ?></span>
      </div>
      <div class="peca-acoes">
        <?php if ($p['stock'] > 0): ?>
          <a class="botao botao-pequeno" href="venda.php?peca_id=<?= $p['id'] ?>">Vender</a>
        <?php else: ?>
          <span class="botao botao-pequeno botao-desativado">Esgotado</span>
        <?php endif; ?>
        <a class="botao botao-pequeno botao-secundario" href="encomenda_fornecedor.php?peca_id=<?= $p['id'] ?>">Encomendar ao fornecedor</a>
      </div>
    </article>
  <?php endforeach; ?>
</div>
<?php endif; ?>
<?php require __DIR__ . '/../templates/rodape.php'; ?>

```

---

## php/public/router.php

```php
<?php
/**
 * Router para o servidor embutido do PHP (php -S ... router.php).
 * Serve ficheiros estáticos (CSS/JS) diretamente e envia o resto para
 * o .php correspondente. Em Apache/XAMPP este ficheiro não é necessário.
 */
$caminho = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($caminho === '/' || $caminho === '') {
    require __DIR__ . '/index.php';
    return true;
}

if (is_file(__DIR__ . $caminho)) {
    return false; // deixa o servidor embutido servir o ficheiro (php ou estático)
}

http_response_code(404);
require __DIR__ . '/../src/bootstrap.php';
$titulo = 'Página não encontrada';
require __DIR__ . '/../templates/cabecalho.php';
echo '<section class="cartao vazio"><h1>Página não encontrada</h1><p>O endereço <code>' . e($caminho) . '</code> não existe.</p><a class="botao" href="index.php">Voltar ao painel</a></section>';
require __DIR__ . '/../templates/rodape.php';
return true;

```

---

## php/public/venda.php

```php
<?php
/**
 * B) Formulário de encomenda/venda de peças a um cliente
 *  - dropdown de clientes (BD1)
 *  - uma ou mais linhas de peça com informação (referência, preço, stock)
 *  - tipo de pagamento escolhido entre os métodos registados em
 *    php/config/pagamentos.php (extensível sem tocar neste ficheiro)
 * Ao submeter, a venda é gravada numa transação e o stock é abatido.
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Repositorios\ClienteRepository;
use AutoLux\Repositorios\PecaRepository;
use AutoLux\Repositorios\VendaRepository;

$titulo = 'Nova venda';
$erros = [];

try {
    $clientes = (new ClienteRepository())->paraDropdown();
    $pecasRepo = new PecaRepository();
    $pecas = $pecasRepo->listar();
} catch (Throwable $excecao) {
    $titulo = 'Base de dados indisponível';
    require __DIR__ . '/../templates/erro.php';
    exit;
}

// Valores iniciais: podem vir por GET a partir do catálogo (?peca_id=) ou dos clientes (?cliente_id=)
$clienteSelecionado = (int) ($_POST['cliente_id'] ?? $_GET['cliente_id'] ?? 0);
$linhas = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idsPeca = $_POST['peca_id'] ?? [];
    $quantidades = $_POST['quantidade'] ?? [];
    foreach ((array) $idsPeca as $i => $pecaId) {
        $linhas[] = ['peca_id' => (int) $pecaId, 'quantidade' => (int) ($quantidades[$i] ?? 0)];
    }
} else {
    $linhas[] = ['peca_id' => (int) ($_GET['peca_id'] ?? 0), 'quantidade' => 1];
}
$metodoSelecionado = $_POST['tipo_pagamento'] ?? '';
$campoExtra = trim($_POST['campo_extra'] ?? '');
$observacoes = trim($_POST['observacoes'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Validação ---------------------------------------------------------
    if ($clienteSelecionado <= 0) {
        $erros['cliente_id'] = 'Selecione o cliente.';
    }

    $itensValidos = [];
    foreach ($linhas as $i => $linha) {
        if ($linha['peca_id'] <= 0 && $linha['quantidade'] <= 0) {
            continue; // linha vazia deixada pelo utilizador
        }
        if ($linha['peca_id'] <= 0) {
            $erros["peca_$i"] = 'Selecione a peça.';
        } elseif ($linha['quantidade'] <= 0) {
            $erros["peca_$i"] = 'A quantidade deve ser pelo menos 1.';
        } else {
            // Junta quantidades da mesma peça repetida em duas linhas
            $itensValidos[$linha['peca_id']] = ($itensValidos[$linha['peca_id']] ?? 0) + $linha['quantidade'];
        }
    }
    if (!$itensValidos && !$erros) {
        $erros['itens'] = 'Adicione pelo menos uma peça à venda.';
    }

    $metodo = $pagamentos->obter($metodoSelecionado);
    if (!$metodo) {
        $erros['tipo_pagamento'] = 'Escolha o tipo de pagamento.';
    } elseif ($metodo->etiquetaCampoExtra() !== null) {
        $erroExtra = $metodo->validarCampoExtra($campoExtra);
        if ($erroExtra) {
            $erros['campo_extra'] = $erroExtra;
        }
    }

    // --- Gravação ---------------------------------------------------------
    if (!$erros) {
        try {
            $itens = [];
            $total = 0.0;
            foreach ($itensValidos as $pecaId => $qtd) {
                $itens[] = ['peca_id' => $pecaId, 'quantidade' => $qtd];
                $peca = $pecasRepo->obter($pecaId);
                $total += $peca ? (float) $peca['preco'] * $qtd : 0;
            }
            $detalhe = $metodo->detalhe($total, $campoExtra);
            $vendaId = (new VendaRepository())->registar(
                $clienteSelecionado, $itens, $metodo->codigo(), $detalhe, $observacoes, (int) $utilizadorAtual['id']
            );
            flash('sucesso', "Venda nº $vendaId registada com sucesso. Pagamento: {$metodo->nome()}" . ($detalhe ? " ($detalhe)" : '') . '.');
            redirecionar("venda_detalhe.php?id=$vendaId");
        } catch (RuntimeException $e) {
            $erros['itens'] = $e->getMessage();
        }
    }
}

// Mapa id -> peça para o JavaScript mostrar preço/stock ao escolher no dropdown
$pecasJson = json_encode(array_map(fn($p) => [
    'id' => (int) $p['id'], 'referencia' => $p['referencia'], 'nome' => $p['nome'], 'marca' => $p['marca'],
    'tipo' => $p['tipo'], 'preco' => (float) $p['preco'], 'stock' => (int) $p['stock'], 'descricao' => $p['descricao'],
], $pecas), JSON_UNESCAPED_UNICODE);

require __DIR__ . '/../templates/cabecalho.php';
?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Vendas</p>
    <h1>Nova venda a cliente</h1>
    <p>Escolha o cliente, as peças e o tipo de pagamento. O stock é atualizado automaticamente.</p>
  </div>
</section>

<?php if (isset($erros['itens'])): ?>
  <div class="alerta alerta-erro"><?= e($erros['itens']) ?></div>
<?php endif; ?>

<form method="post" action="venda.php" class="formulario formulario-venda" id="form-venda" novalidate>
  <?= campoCsrf() ?>
  <div class="duas-colunas colunas-2-1">
    <div>
      <section class="cartao">
        <h2>1. Cliente</h2>
        <label>Cliente *
          <select name="cliente_id" required>
            <option value="">— Selecione o cliente —</option>
            <?php foreach ($clientes as $c): ?>
              <option value="<?= $c['id'] ?>" <?= $c['id'] == $clienteSelecionado ? 'selected' : '' ?>><?= e($c['nome']) ?> (NIF <?= e($c['nif']) ?>)</option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($erros['cliente_id'])): ?><span class="erro-campo"><?= e($erros['cliente_id']) ?></span><?php endif; ?>
        </label>
        <p class="texto-suave">Cliente novo? <a href="clientes.php">Registe-o primeiro</a>.</p>
      </section>

      <section class="cartao">
        <div class="cartao-topo">
          <h2>2. Peças</h2>
          <button type="button" class="botao botao-pequeno botao-secundario" id="adicionar-linha">+ Adicionar peça</button>
        </div>

        <div id="linhas-pecas">
          <?php foreach ($linhas as $i => $linha): ?>
          <div class="linha-peca" data-indice="<?= $i ?>">
            <label>Peça *
              <select name="peca_id[]" class="select-peca" required>
                <option value="">— Selecione a peça —</option>
                <?php foreach ($pecas as $p): ?>
                  <option value="<?= $p['id'] ?>" <?= $p['id'] == $linha['peca_id'] ? 'selected' : '' ?> <?= $p['stock'] <= 0 ? 'disabled' : '' ?>>
                    <?= e($p['referencia']) ?> · <?= e($p['marca']) ?> <?= e($p['nome']) ?> — <?= formatarPreco($p['preco']) ?> (stock <?= $p['stock'] ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>Qtd. *
              <input type="number" name="quantidade[]" class="input-quantidade" min="1" value="<?= $linha['quantidade'] ?: 1 ?>" required>
            </label>
            <button type="button" class="botao-icone remover-linha" title="Remover linha" aria-label="Remover linha">×</button>
            <div class="info-peca texto-suave"></div>
            <?php if (isset($erros["peca_$i"])): ?><span class="erro-campo"><?= e($erros["peca_$i"]) ?></span><?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>

        <template id="template-linha">
          <div class="linha-peca">
            <label>Peça *
              <select name="peca_id[]" class="select-peca" required>
                <option value="">— Selecione a peça —</option>
                <?php foreach ($pecas as $p): ?>
                  <option value="<?= $p['id'] ?>" <?= $p['stock'] <= 0 ? 'disabled' : '' ?>>
                    <?= e($p['referencia']) ?> · <?= e($p['marca']) ?> <?= e($p['nome']) ?> — <?= formatarPreco($p['preco']) ?> (stock <?= $p['stock'] ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>Qtd. *
              <input type="number" name="quantidade[]" class="input-quantidade" min="1" value="1" required>
            </label>
            <button type="button" class="botao-icone remover-linha" title="Remover linha" aria-label="Remover linha">×</button>
            <div class="info-peca texto-suave"></div>
          </div>
        </template>
      </section>

      <section class="cartao">
        <h2>3. Tipo de pagamento</h2>
        <?php if (isset($erros['tipo_pagamento'])): ?><p class="erro-campo"><?= e($erros['tipo_pagamento']) ?></p><?php endif; ?>
        <div class="opcoes-pagamento">
          <?php foreach ($pagamentos->todos() as $m): ?>
            <label class="opcao-pagamento">
              <input type="radio" name="tipo_pagamento" value="<?= e($m->codigo()) ?>"
                     data-campo-extra="<?= e($m->etiquetaCampoExtra() ?? '') ?>"
                     <?= $m->codigo() === $metodoSelecionado ? 'checked' : '' ?> required>
              <span>
                <strong><?= e($m->nome()) ?></strong>
                <small><?= e($m->descricao()) ?></small>
              </span>
            </label>
          <?php endforeach; ?>
        </div>
        <label id="grupo-campo-extra" hidden>
          <span id="rotulo-campo-extra"></span>
          <input type="text" name="campo_extra" value="<?= e($campoExtra) ?>">
          <?php if (isset($erros['campo_extra'])): ?><span class="erro-campo"><?= e($erros['campo_extra']) ?></span><?php endif; ?>
        </label>
        <p class="texto-suave nota">Os métodos disponíveis são definidos em <code>php/config/pagamentos.php</code>. Para adicionar um novo basta criar uma classe que implemente <code>MetodoPagamento</code> e registá-la lá.</p>
      </section>
    </div>

    <aside class="cartao resumo-venda">
      <h2>Resumo</h2>
      <ul id="resumo-itens" class="lista-resumo"><li class="texto-suave">Nenhuma peça selecionada.</li></ul>
      <div class="total-linha"><span>Total</span><strong id="resumo-total">0,00 €</strong></div>
      <label>Observações
        <textarea name="observacoes" rows="3" maxlength="255"><?= e($observacoes) ?></textarea>
      </label>
      <button class="botao botao-largo" type="submit">Registar venda</button>
      <a class="botao botao-secundario botao-largo" href="pecas.php">Voltar ao catálogo</a>
    </aside>
  </div>
</form>

<script id="dados-pecas" type="application/json"><?= $pecasJson ?></script>
<?php require __DIR__ . '/../templates/rodape.php'; ?>

```

---

## php/public/venda_detalhe.php

```php
<?php
/** Detalhe/recibo de uma venda. */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Repositorios\VendaRepository;

$titulo = 'Detalhe da venda';
$id = (int) ($_GET['id'] ?? 0);

try {
    $venda = $id > 0 ? (new VendaRepository())->obter($id) : null;
} catch (Throwable $excecao) {
    $titulo = 'Base de dados indisponível';
    require __DIR__ . '/../templates/erro.php';
    exit;
}

require __DIR__ . '/../templates/cabecalho.php';

if (!$venda): ?>
  <section class="cartao vazio">
    <h1>Venda não encontrada</h1>
    <p>Não existe nenhuma venda com o número <?= $id ?>.</p>
    <a class="botao" href="vendas.php">Voltar às vendas</a>
  </section>
<?php else: ?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Venda</p>
    <h1>Venda nº <?= $venda['id'] ?></h1>
    <p><?= formatarData($venda['data_venda']) ?> · registada por <?= e($venda['funcionario'] ?? 'funcionário removido') ?></p>
  </div>
  <div class="acoes">
    <a href="venda.php?cliente_id=<?= $venda['cliente_id'] ?>" class="botao botao-secundario">Nova venda a este cliente</a>
    <a href="vendas.php" class="botao botao-secundario">Todas as vendas</a>
  </div>
</section>

<div class="duas-colunas colunas-2-1">
  <section class="cartao">
    <h2>Peças vendidas</h2>
    <table class="tabela">
      <thead><tr><th>Referência</th><th>Peça</th><th class="num">Qtd.</th><th class="num">Preço unit.</th><th class="num">Subtotal</th></tr></thead>
      <tbody>
        <?php foreach ($venda['itens'] as $i): ?>
        <tr>
          <td><code><?= e($i['referencia']) ?></code></td>
          <td><?= e($i['peca']) ?></td>
          <td class="num"><?= $i['quantidade'] ?></td>
          <td class="num"><?= formatarPreco($i['preco_unitario']) ?></td>
          <td class="num"><?= formatarPreco($i['subtotal']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot><tr><th colspan="4" class="num">Total</th><th class="num"><?= formatarPreco($venda['total']) ?></th></tr></tfoot>
    </table>
  </section>

  <aside>
    <section class="cartao">
      <h2>Cliente</h2>
      <p><strong><?= e($venda['cliente']) ?></strong><br>NIF <?= e($venda['cliente_nif']) ?><br><?= e($venda['cliente_email']) ?></p>
    </section>
    <section class="cartao">
      <h2>Pagamento</h2>
      <p><strong><?= e($pagamentos->nomePorCodigo($venda['tipo_pagamento'])) ?></strong></p>
      <?php if ($venda['detalhe_pagamento']): ?><p class="texto-suave"><?= e($venda['detalhe_pagamento']) ?></p><?php endif; ?>
      <?php if ($venda['observacoes']): ?><p><em><?= e($venda['observacoes']) ?></em></p><?php endif; ?>
    </section>
  </aside>
</div>
<?php endif; ?>
<?php require __DIR__ . '/../templates/rodape.php'; ?>

```

---

## php/public/vendas.php

```php
<?php
/** Histórico de vendas registadas (BD1). */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Repositorios\VendaRepository;

$titulo = 'Vendas';

try {
    $repo = new VendaRepository();
    $lista = $repo->listar(200);
    $resumo = $repo->resumo();
} catch (Throwable $excecao) {
    $titulo = 'Base de dados indisponível';
    require __DIR__ . '/../templates/erro.php';
    exit;
}

require __DIR__ . '/../templates/cabecalho.php';
?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Vendas</p>
    <h1>Histórico de vendas</h1>
    <p><?= $resumo['num_vendas'] ?> venda(s) · faturação total <?= formatarPreco($resumo['faturacao']) ?></p>
  </div>
  <a href="venda.php" class="botao">+ Nova venda</a>
</section>

<section class="cartao">
  <table class="tabela">
    <thead>
      <tr><th>#</th><th>Data</th><th>Cliente</th><th>Funcionário</th><th>Pagamento</th><th class="num">Linhas</th><th class="num">Unidades</th><th class="num">Total</th><th></th></tr>
    </thead>
    <tbody>
      <?php if (!$lista): ?>
        <tr><td colspan="9" class="texto-suave">Ainda não há vendas registadas.</td></tr>
      <?php endif; ?>
      <?php foreach ($lista as $v): ?>
      <tr>
        <td>#<?= $v['id'] ?></td>
        <td><?= formatarData($v['data_venda']) ?></td>
        <td><?= e($v['cliente']) ?></td>
        <td><?= e($v['funcionario'] ?? '—') ?></td>
        <td><?= e($pagamentos->nomePorCodigo($v['tipo_pagamento'])) ?><br><small class="texto-suave"><?= e($v['detalhe_pagamento']) ?></small></td>
        <td class="num"><?= $v['num_itens'] ?></td>
        <td class="num"><?= $v['unidades'] ?></td>
        <td class="num"><strong><?= formatarPreco($v['total']) ?></strong></td>
        <td><a class="botao botao-pequeno botao-secundario" href="venda_detalhe.php?id=<?= $v['id'] ?>">Detalhe</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>
<?php require __DIR__ . '/../templates/rodape.php'; ?>

```

---

## php/src/Api/ApiException.php

```php
<?php
declare(strict_types=1);

namespace AutoLux\Api;

/** Erro devolvido pela Web API Node.js ou falha de comunicação com ela. */
final class ApiException extends \RuntimeException
{
}

```

---

## php/src/Api/FornecedoresApiClient.php

```php
<?php
declare(strict_types=1);

namespace AutoLux\Api;

/**
 * Cliente HTTP (cURL) para a Web API Node.js de fornecedores/encomendas.
 *
 * O PHP nunca liga diretamente à Base de Dados 2: todos os dados de
 * fornecedores e encomendas passam por esta classe -> HTTP -> Node.js -> MySQL.
 */
final class FornecedoresApiClient
{
    public function __construct(private string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    // ----- Fornecedores ---------------------------------------------------

    public function listarFornecedores(bool $incluirInativos = false): array
    {
        return $this->pedido('GET', '/fornecedores' . ($incluirInativos ? '?todos=1' : ''));
    }

    public function obterFornecedor(int $id): array
    {
        return $this->pedido('GET', "/fornecedores/$id");
    }

    public function criarFornecedor(array $dados): array
    {
        return $this->pedido('POST', '/fornecedores', $dados);
    }

    // ----- Encomendas -----------------------------------------------------

    public function listarEncomendas(array $filtros = []): array
    {
        $query = http_build_query(array_filter($filtros, fn($v) => $v !== '' && $v !== null));
        return $this->pedido('GET', '/encomendas' . ($query ? "?$query" : ''));
    }

    public function obterEncomenda(int $id): array
    {
        return $this->pedido('GET', "/encomendas/$id");
    }

    /**
     * @param array<int, array{referencia_peca:string, descricao:string, quantidade:int, preco_unitario:float}> $itens
     */
    public function submeterEncomenda(int $fornecedorId, array $itens, ?string $observacoes = null, ?string $criadoPor = null): array
    {
        return $this->pedido('POST', '/encomendas', [
            'fornecedor_id' => $fornecedorId,
            'observacoes'   => $observacoes,
            'criado_por'    => $criadoPor,
            'itens'         => $itens,
        ]);
    }

    public function alterarEstado(int $id, string $estado): array
    {
        return $this->pedido('PATCH', "/encomendas/$id/estado", ['estado' => $estado]);
    }

    public function saudavel(): bool
    {
        try {
            return ($this->pedido('GET', '/health')['estado'] ?? '') === 'ok';
        } catch (\Throwable) {
            return false;
        }
    }

    // ----- Interno ----------------------------------------------------------

    /**
     * Executa o pedido HTTP e devolve o JSON já descodificado.
     * Erros da API (4xx/5xx) são convertidos em ApiException com a mensagem da API.
     */
    private function pedido(string $metodo, string $caminho, ?array $corpo = null): array
    {
        $ch = curl_init($this->baseUrl . $caminho);
        $opcoes = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $metodo,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_HTTPHEADER     => ['Accept: application/json', 'Content-Type: application/json'],
        ];
        if ($corpo !== null) {
            $opcoes[CURLOPT_POSTFIELDS] = json_encode($corpo, JSON_UNESCAPED_UNICODE);
        }
        curl_setopt_array($ch, $opcoes);

        $resposta = curl_exec($ch);
        $erroCurl = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($resposta === false) {
            throw new ApiException(
                'Não foi possível contactar a API de fornecedores em ' . $this->baseUrl .
                '. Confirme que o serviço Node.js está a correr (npm run start:api). Detalhe: ' . $erroCurl,
                0
            );
        }

        $dados = json_decode($resposta, true);
        if (!is_array($dados)) {
            throw new ApiException('Resposta inválida da API (não é JSON).', $status);
        }

        if ($status >= 400) {
            $mensagem = $dados['erro'] ?? 'Erro na API';
            if (!empty($dados['detalhes'])) {
                $mensagem .= ': ' . implode('; ', (array) $dados['detalhes']);
            }
            throw new ApiException($mensagem, $status);
        }

        return $dados;
    }
}

```

---

## php/src/Auth.php

```php
<?php
declare(strict_types=1);

namespace AutoLux;

use AutoLux\Repositorios\FuncionarioRepository;

/**
 * Autenticação de funcionários baseada em sessão PHP.
 *
 * Fluxo:
 *   login.php  -> Auth::entrar(utilizador, password)  -> guarda o id na sessão
 *   qualquer página -> Auth::exigirAutenticacao() (chamado no bootstrap)
 *   logout.php -> Auth::sair()
 *
 * Só o id do funcionário fica na sessão; os dados são relidos da BD em cada
 * pedido, para que desativar um funcionário tenha efeito imediato.
 */
final class Auth
{
    private const CHAVE_SESSAO = 'funcionario_id';

    /** Nº máximo de tentativas falhadas seguidas antes de bloquear temporariamente. */
    private const MAX_TENTATIVAS = 5;
    private const BLOQUEIO_SEGUNDOS = 60;

    private static ?array $utilizadorAtual = null;
    private static bool $carregado = false;

    /** Funcionário autenticado (array da tabela) ou null. */
    public static function utilizador(): ?array
    {
        if (!self::$carregado) {
            self::$carregado = true;
            $id = $_SESSION[self::CHAVE_SESSAO] ?? null;
            if ($id) {
                $f = (new FuncionarioRepository())->obter((int) $id);
                if ($f && $f['ativo']) {
                    self::$utilizadorAtual = $f;
                } else {
                    unset($_SESSION[self::CHAVE_SESSAO]); // conta apagada ou desativada entretanto
                }
            }
        }
        return self::$utilizadorAtual;
    }

    public static function autenticado(): bool
    {
        return self::utilizador() !== null;
    }

    public static function ehAdmin(): bool
    {
        return (self::utilizador()['perfil'] ?? '') === 'admin';
    }

    /**
     * Tenta autenticar. Devolve null em caso de sucesso ou a mensagem de erro.
     * A mensagem é propositadamente genérica para não revelar se o utilizador existe.
     */
    public static function entrar(string $utilizador, string $password): ?string
    {
        if (self::bloqueado()) {
            return 'Demasiadas tentativas falhadas. Aguarde ' . self::BLOQUEIO_SEGUNDOS . ' segundos e tente novamente.';
        }

        $repo = new FuncionarioRepository();
        $f = $repo->obterPorUtilizador(strtolower(trim($utilizador)));

        // password_verify compara a password com o hash bcrypt em tempo constante
        if (!$f || !password_verify($password, $f['password_hash'])) {
            self::registarFalha();
            return 'Utilizador ou password incorretos.';
        }
        if (!$f['ativo']) {
            return 'Esta conta está desativada. Contacte o administrador.';
        }

        // Novo id de sessão após login: evita ataques de "session fixation"
        session_regenerate_id(true);
        $_SESSION[self::CHAVE_SESSAO] = (int) $f['id'];
        unset($_SESSION['login_falhas'], $_SESSION['login_bloqueio_ate']);
        $repo->registarLogin((int) $f['id']);

        self::$carregado = false;
        return null;
    }

    /**
     * Termina a sessão: apaga todos os dados e troca o id de sessão (o antigo
     * é destruído no servidor). Mantém-se uma sessão vazia para poder mostrar
     * a mensagem "sessão terminada" na página de login.
     */
    public static function sair(): void
    {
        $_SESSION = [];
        session_regenerate_id(true);
        self::$utilizadorAtual = null;
        self::$carregado = true;
    }

    /** Redireciona para o login (guardando a página pedida) se não houver sessão. */
    public static function exigirAutenticacao(): void
    {
        if (self::autenticado()) {
            return;
        }
        $destino = ltrim($_SERVER['REQUEST_URI'] ?? '', '/') ?: 'index.php';
        header('Location: login.php?redirect=' . urlencode($destino));
        exit;
    }

    /** Bloqueia a página a quem não for administrador. */
    public static function exigirAdmin(): void
    {
        self::exigirAutenticacao();
        if (!self::ehAdmin()) {
            http_response_code(403);
            flash('erro', 'Não tem permissões para aceder a essa página (apenas administradores).');
            header('Location: index.php');
            exit;
        }
    }

    // ----- Proteção simples contra força bruta (por sessão) -----------------

    private static function bloqueado(): bool
    {
        $ate = $_SESSION['login_bloqueio_ate'] ?? 0;
        if ($ate > time()) {
            return true;
        }
        if ($ate) {
            unset($_SESSION['login_bloqueio_ate'], $_SESSION['login_falhas']);
        }
        return false;
    }

    private static function registarFalha(): void
    {
        $_SESSION['login_falhas'] = ($_SESSION['login_falhas'] ?? 0) + 1;
        if ($_SESSION['login_falhas'] >= self::MAX_TENTATIVAS) {
            $_SESSION['login_bloqueio_ate'] = time() + self::BLOQUEIO_SEGUNDOS;
        }
    }
}

```

---

## php/src/Database.php

```php
<?php
declare(strict_types=1);

namespace AutoLux;

use PDO;
use PDOException;

/**
 * Ligação única (singleton) à Base de Dados 1 através de PDO.
 * Todos os repositórios pedem a ligação aqui: Database::ligacao().
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function ligacao(): PDO
    {
        if (self::$pdo === null) {
            $cfg = $GLOBALS['config'];
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $cfg['DB_HOST'],
                $cfg['DB_PORT'],
                $cfg['DB1_NAME']
            );

            try {
                self::$pdo = new PDO($dsn, $cfg['DB_USER'], $cfg['DB_PASSWORD'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // erros SQL lançam exceções
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // linhas como arrays associativos
                    PDO::ATTR_EMULATE_PREPARES   => false,                  // prepared statements reais (segurança)
                ]);
            } catch (PDOException $e) {
                throw new \RuntimeException(
                    'Não foi possível ligar à base de dados "' . $cfg['DB1_NAME'] . '". ' .
                    'Confirme que o MySQL está a correr, que executou "npm run db:setup" e que o ficheiro .env está correto. ' .
                    'Detalhe: ' . $e->getMessage()
                );
            }
        }

        return self::$pdo;
    }
}

```

---

## php/src/Pagamentos/CartaoCredito.php

```php
<?php
declare(strict_types=1);

namespace AutoLux\Pagamentos;

final class CartaoCredito implements MetodoPagamento
{
    public function codigo(): string { return 'cartao'; }
    public function nome(): string { return 'Cartão de crédito / débito'; }
    public function descricao(): string { return 'Pagamento no terminal TPA da loja.'; }
    public function etiquetaCampoExtra(): ?string { return 'Últimos 4 dígitos do cartão'; }

    public function validarCampoExtra(?string $valor): ?string
    {
        return preg_match('/^\d{4}$/', (string) $valor) ? null : 'Indique exatamente os últimos 4 dígitos do cartão.';
    }

    public function detalhe(float $total, ?string $valorExtra): ?string
    {
        return 'Cartão terminado em ' . $valorExtra;
    }
}

```

---

## php/src/Pagamentos/Dinheiro.php

```php
<?php
declare(strict_types=1);

namespace AutoLux\Pagamentos;

final class Dinheiro implements MetodoPagamento
{
    public function codigo(): string { return 'dinheiro'; }
    public function nome(): string { return 'Dinheiro'; }
    public function descricao(): string { return 'Pagamento em numerário ao balcão.'; }
    public function etiquetaCampoExtra(): ?string { return null; }
    public function validarCampoExtra(?string $valor): ?string { return null; }
    public function detalhe(float $total, ?string $valorExtra): ?string { return 'Pago em numerário'; }
}

```

---

## php/src/Pagamentos/MBWay.php

```php
<?php
declare(strict_types=1);

namespace AutoLux\Pagamentos;

final class MBWay implements MetodoPagamento
{
    public function codigo(): string { return 'mbway'; }
    public function nome(): string { return 'MB WAY'; }
    public function descricao(): string { return 'Pedido de pagamento enviado para o telemóvel do cliente.'; }
    public function etiquetaCampoExtra(): ?string { return 'Nº de telemóvel (9 dígitos)'; }

    public function validarCampoExtra(?string $valor): ?string
    {
        $valor = preg_replace('/\s+/', '', (string) $valor);
        return preg_match('/^9\d{8}$/', $valor) ? null : 'Indique um número de telemóvel português válido (9 dígitos, começa por 9).';
    }

    public function detalhe(float $total, ?string $valorExtra): ?string
    {
        return 'MB WAY para ' . preg_replace('/\s+/', '', (string) $valorExtra);
    }
}

```

---

## php/src/Pagamentos/MetodoPagamento.php

```php
<?php
declare(strict_types=1);

namespace AutoLux\Pagamentos;

/**
 * Contrato que todos os métodos de pagamento têm de cumprir.
 *
 * Para adicionar um novo tipo de pagamento basta:
 *   1. Criar uma classe nesta pasta que implemente esta interface;
 *   2. Registá-la em php/config/pagamentos.php.
 * Não é necessário alterar o formulário nem a base de dados.
 */
interface MetodoPagamento
{
    /** Código curto e único guardado na coluna vendas.tipo_pagamento (ex.: "mbway"). */
    public function codigo(): string;

    /** Nome apresentado ao utilizador no formulário. */
    public function nome(): string;

    /** Texto de ajuda mostrado por baixo da opção. */
    public function descricao(): string;

    /**
     * Indica se este método precisa de um campo extra (nº de telemóvel,
     * referência, últimos dígitos do cartão...). Devolve null se não precisar.
     */
    public function etiquetaCampoExtra(): ?string;

    /**
     * Valida o valor do campo extra. Devolve uma mensagem de erro ou null se estiver OK.
     */
    public function validarCampoExtra(?string $valor): ?string;

    /**
     * Devolve o texto a guardar em vendas.detalhe_pagamento (ex.: referência gerada).
     * Recebe o total da venda para métodos que geram referências.
     */
    public function detalhe(float $total, ?string $valorExtra): ?string;
}

```

---

## php/src/Pagamentos/Multibanco.php

```php
<?php
declare(strict_types=1);

namespace AutoLux\Pagamentos;

/** Gera uma referência Multibanco (simulada) para o cliente pagar. */
final class Multibanco implements MetodoPagamento
{
    public function codigo(): string { return 'multibanco'; }
    public function nome(): string { return 'Referência Multibanco'; }
    public function descricao(): string { return 'É gerada uma referência para pagamento em ATM ou homebanking.'; }
    public function etiquetaCampoExtra(): ?string { return null; }
    public function validarCampoExtra(?string $valor): ?string { return null; }

    public function detalhe(float $total, ?string $valorExtra): ?string
    {
        $referencia = str_pad((string) random_int(0, 999999999), 9, '0', STR_PAD_LEFT);
        return sprintf('Entidade 21 000 | Ref. %s %s %s | %.2f €',
            substr($referencia, 0, 3), substr($referencia, 3, 3), substr($referencia, 6, 3), $total);
    }
}

```

---

## php/src/Pagamentos/RegistoPagamentos.php

```php
<?php
declare(strict_types=1);

namespace AutoLux\Pagamentos;

/**
 * Coleção dos métodos de pagamento disponíveis. É preenchida a partir de
 * php/config/pagamentos.php e usada pelo formulário de venda para
 * apresentar as opções e validar o método escolhido.
 */
final class RegistoPagamentos
{
    /** @var array<string, MetodoPagamento> indexado pelo código */
    private array $metodos = [];

    /** @param class-string<MetodoPagamento>[] $classes */
    public function __construct(array $classes)
    {
        foreach ($classes as $classe) {
            $metodo = new $classe();
            if (!$metodo instanceof MetodoPagamento) {
                throw new \InvalidArgumentException("$classe não implementa MetodoPagamento");
            }
            $this->metodos[$metodo->codigo()] = $metodo;
        }
    }

    /** @return MetodoPagamento[] */
    public function todos(): array
    {
        return array_values($this->metodos);
    }

    public function obter(?string $codigo): ?MetodoPagamento
    {
        return $this->metodos[$codigo ?? ''] ?? null;
    }

    public function nomePorCodigo(string $codigo): string
    {
        return $this->metodos[$codigo]?->nome() ?? ucfirst($codigo);
    }
}

```

---

## php/src/Pagamentos/Transferencia.php

```php
<?php
declare(strict_types=1);

namespace AutoLux\Pagamentos;

final class Transferencia implements MetodoPagamento
{
    public function codigo(): string { return 'transferencia'; }
    public function nome(): string { return 'Transferência bancária'; }
    public function descricao(): string { return 'Para clientes empresariais com conta corrente. Pagamento a 30 dias.'; }
    public function etiquetaCampoExtra(): ?string { return null; }
    public function validarCampoExtra(?string $valor): ?string { return null; }

    public function detalhe(float $total, ?string $valorExtra): ?string
    {
        $vencimento = (new \DateTimeImmutable('+30 days'))->format('d/m/Y');
        return 'IBAN PT50 0000 0000 0000 0000 0000 0 | vencimento ' . $vencimento;
    }
}

```

---

## php/src/Repositorios/ClienteRepository.php

```php
<?php
declare(strict_types=1);

namespace AutoLux\Repositorios;

use AutoLux\Database;
use PDO;

/** Acesso à tabela `clientes`. */
final class ClienteRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::ligacao();
    }

    public function listar(string $pesquisa = ''): array
    {
        $sql = 'SELECT c.*,
                       COUNT(v.id)            AS num_vendas,
                       COALESCE(SUM(v.total), 0) AS total_gasto
                  FROM clientes c
                  LEFT JOIN vendas v ON v.cliente_id = c.id';
        $params = [];
        if ($pesquisa !== '') {
            $sql .= ' WHERE c.nome LIKE :p OR c.nif LIKE :p OR c.email LIKE :p';
            $params['p'] = "%$pesquisa%";
        }
        $sql .= ' GROUP BY c.id ORDER BY c.nome';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Versão leve para preencher dropdowns. */
    public function paraDropdown(): array
    {
        return $this->pdo->query('SELECT id, nome, nif FROM clientes ORDER BY nome')->fetchAll();
    }

    public function obter(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM clientes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /** @return array<string,string> erros de validação indexados por campo (vazio = OK) */
    public function validar(array $dados): array
    {
        $erros = [];
        if (mb_strlen(trim($dados['nome'] ?? '')) < 3) {
            $erros['nome'] = 'O nome deve ter pelo menos 3 caracteres.';
        }
        if (!preg_match('/^\d{9}$/', $dados['nif'] ?? '')) {
            $erros['nif'] = 'O NIF deve ter 9 dígitos.';
        }
        if (!filter_var($dados['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $erros['email'] = 'Introduza um email válido.';
        }
        return $erros;
    }

    public function criar(array $dados): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO clientes (nome, nif, email, telefone, morada)
             VALUES (:nome, :nif, :email, :telefone, :morada)'
        );
        $stmt->execute([
            'nome'     => trim($dados['nome']),
            'nif'      => $dados['nif'],
            'email'    => trim($dados['email']),
            'telefone' => trim($dados['telefone'] ?? '') ?: null,
            'morada'   => trim($dados['morada'] ?? '') ?: null,
        ]);
        return (int) $this->pdo->lastInsertId();
    }
}

```

---

## php/src/Repositorios/FuncionarioRepository.php

```php
<?php
declare(strict_types=1);

namespace AutoLux\Repositorios;

use AutoLux\Database;
use PDO;

/** Acesso à tabela `funcionarios` (utilizadores da aplicação). */
final class FuncionarioRepository
{
    public const PERFIS = ['admin' => 'Administrador', 'funcionario' => 'Funcionário'];

    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::ligacao();
    }

    public function listar(): array
    {
        return $this->pdo->query(
            'SELECT f.*, COUNT(v.id) AS num_vendas
               FROM funcionarios f
               LEFT JOIN vendas v ON v.funcionario_id = f.id
              GROUP BY f.id
              ORDER BY f.nome'
        )->fetchAll();
    }

    public function obter(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM funcionarios WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /** Procura pelo nome de utilizador (usado no login). Inclui inativos: quem chama decide. */
    public function obterPorUtilizador(string $utilizador): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM funcionarios WHERE utilizador = :u');
        $stmt->execute(['u' => $utilizador]);
        return $stmt->fetch() ?: null;
    }

    /** @return array<string,string> erros por campo (vazio = OK) */
    public function validar(array $dados, bool $exigirPassword = true): array
    {
        $erros = [];
        if (mb_strlen(trim($dados['nome'] ?? '')) < 3) {
            $erros['nome'] = 'O nome deve ter pelo menos 3 caracteres.';
        }
        if (!preg_match('/^[a-z0-9._-]{3,40}$/i', $dados['utilizador'] ?? '')) {
            $erros['utilizador'] = 'Utilizador: 3 a 40 caracteres (letras, números, ponto, hífen ou underscore).';
        }
        if (!filter_var($dados['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $erros['email'] = 'Introduza um email válido.';
        }
        if (!isset(self::PERFIS[$dados['perfil'] ?? ''])) {
            $erros['perfil'] = 'Perfil inválido.';
        }
        if ($exigirPassword || ($dados['password'] ?? '') !== '') {
            $erroPw = self::validarPassword($dados['password'] ?? '');
            if ($erroPw) {
                $erros['password'] = $erroPw;
            }
        }
        return $erros;
    }

    public static function validarPassword(string $password): ?string
    {
        return mb_strlen($password) < 6 ? 'A password deve ter pelo menos 6 caracteres.' : null;
    }

    public function criar(array $dados): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO funcionarios (nome, utilizador, email, password_hash, perfil)
             VALUES (:nome, :utilizador, :email, :hash, :perfil)'
        );
        $stmt->execute([
            'nome'       => trim($dados['nome']),
            'utilizador' => strtolower(trim($dados['utilizador'])),
            'email'      => trim($dados['email']),
            // password_hash gera um hash bcrypt com salt aleatório; nunca guardamos a password em claro
            'hash'       => password_hash($dados['password'], PASSWORD_DEFAULT),
            'perfil'     => $dados['perfil'],
        ]);
        return (int) $this->pdo->lastInsertId();
    }

    public function alterarPassword(int $id, string $novaPassword): void
    {
        $stmt = $this->pdo->prepare('UPDATE funcionarios SET password_hash = :hash WHERE id = :id');
        $stmt->execute(['hash' => password_hash($novaPassword, PASSWORD_DEFAULT), 'id' => $id]);
    }

    public function alterarAtivo(int $id, bool $ativo): void
    {
        $stmt = $this->pdo->prepare('UPDATE funcionarios SET ativo = :ativo WHERE id = :id');
        $stmt->execute(['ativo' => (int) $ativo, 'id' => $id]);
    }

    public function registarLogin(int $id): void
    {
        $this->pdo->prepare('UPDATE funcionarios SET ultimo_login = NOW() WHERE id = :id')->execute(['id' => $id]);
    }

    public function contarAdminsAtivos(): int
    {
        return (int) $this->pdo->query("SELECT COUNT(*) FROM funcionarios WHERE perfil = 'admin' AND ativo = 1")->fetchColumn();
    }
}

```

---

## php/src/Repositorios/PecaRepository.php

```php
<?php
declare(strict_types=1);

namespace AutoLux\Repositorios;

use AutoLux\Database;
use PDO;

/** Acesso à tabela `pecas` (catálogo / stock). */
final class PecaRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::ligacao();
    }

    /**
     * Lista peças aplicando os filtros do catálogo.
     * Todos os filtros são opcionais; os valores vão sempre por parâmetros
     * (prepared statements) para evitar SQL injection.
     *
     * @param array{marca_id?:string, tipo_id?:string, preco_min?:string, preco_max?:string, pesquisa?:string, so_stock_baixo?:string} $filtros
     */
    public function listar(array $filtros = []): array
    {
        $sql = 'SELECT p.*, m.nome AS marca, t.nome AS tipo
                  FROM pecas p
                  JOIN marcas m ON m.id = p.marca_id
                  JOIN tipos_peca t ON t.id = p.tipo_id';
        $condicoes = [];
        $params = [];

        if (!empty($filtros['marca_id'])) {
            $condicoes[] = 'p.marca_id = :marca_id';
            $params['marca_id'] = (int) $filtros['marca_id'];
        }
        if (!empty($filtros['tipo_id'])) {
            $condicoes[] = 'p.tipo_id = :tipo_id';
            $params['tipo_id'] = (int) $filtros['tipo_id'];
        }
        if (isset($filtros['preco_min']) && $filtros['preco_min'] !== '') {
            $condicoes[] = 'p.preco >= :preco_min';
            $params['preco_min'] = (float) $filtros['preco_min'];
        }
        if (isset($filtros['preco_max']) && $filtros['preco_max'] !== '') {
            $condicoes[] = 'p.preco <= :preco_max';
            $params['preco_max'] = (float) $filtros['preco_max'];
        }
        if (!empty($filtros['pesquisa'])) {
            $condicoes[] = '(p.nome LIKE :pesquisa OR p.referencia LIKE :pesquisa OR p.descricao LIKE :pesquisa)';
            $params['pesquisa'] = '%' . $filtros['pesquisa'] . '%';
        }
        if (!empty($filtros['so_stock_baixo'])) {
            $condicoes[] = 'p.stock <= p.stock_minimo';
        }

        if ($condicoes) {
            $sql .= ' WHERE ' . implode(' AND ', $condicoes);
        }
        $sql .= ' ORDER BY m.nome, p.nome';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function obter(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*, m.nome AS marca, t.nome AS tipo
               FROM pecas p
               JOIN marcas m ON m.id = p.marca_id
               JOIN tipos_peca t ON t.id = p.tipo_id
              WHERE p.id = :id'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function marcas(): array
    {
        return $this->pdo->query('SELECT id, nome FROM marcas ORDER BY nome')->fetchAll();
    }

    public function tiposPeca(): array
    {
        return $this->pdo->query('SELECT id, nome FROM tipos_peca ORDER BY nome')->fetchAll();
    }

    /** Intervalo de preços do catálogo, usado para sugerir limites nos filtros. */
    public function intervaloPrecos(): array
    {
        return $this->pdo->query('SELECT MIN(preco) AS minimo, MAX(preco) AS maximo FROM pecas')->fetch();
    }

    public function contarStockBaixo(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM pecas WHERE stock <= stock_minimo')->fetchColumn();
    }
}

```

---

## php/src/Repositorios/VendaRepository.php

```php
<?php
declare(strict_types=1);

namespace AutoLux\Repositorios;

use AutoLux\Database;
use PDO;
use RuntimeException;

/**
 * Registo e consulta de vendas (tabelas `vendas` e `vendas_itens`).
 * O registo de uma venda também abate o stock das peças vendidas.
 */
final class VendaRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::ligacao();
    }

    /**
     * Regista uma venda com um ou mais itens dentro de uma transação:
     * cabeçalho + linhas + atualização de stock ficam gravados todos, ou nenhum.
     *
     * @param array<int, array{peca_id:int, quantidade:int}> $itens
     * @return int id da venda criada
     * @throws RuntimeException se não houver stock suficiente
     */
    public function registar(int $clienteId, array $itens, string $tipoPagamento, ?string $detalhePagamento, ?string $observacoes, ?int $funcionarioId = null): int
    {
        $this->pdo->beginTransaction();
        try {
            $total = 0.0;
            $linhas = [];

            // Bloqueia as linhas das peças (FOR UPDATE) para evitar que duas vendas
            // simultâneas vendam o mesmo stock.
            $stmtPeca = $this->pdo->prepare('SELECT id, nome, preco, stock FROM pecas WHERE id = :id FOR UPDATE');
            foreach ($itens as $item) {
                $stmtPeca->execute(['id' => $item['peca_id']]);
                $peca = $stmtPeca->fetch();
                if (!$peca) {
                    throw new RuntimeException('Peça inexistente (id ' . $item['peca_id'] . ').');
                }
                if ($peca['stock'] < $item['quantidade']) {
                    throw new RuntimeException(sprintf(
                        'Stock insuficiente para "%s": disponível %d, pedido %d.',
                        $peca['nome'], $peca['stock'], $item['quantidade']
                    ));
                }
                $linhas[] = ['peca' => $peca, 'quantidade' => $item['quantidade']];
                $total += (float) $peca['preco'] * $item['quantidade'];
            }

            $stmtVenda = $this->pdo->prepare(
                'INSERT INTO vendas (cliente_id, funcionario_id, tipo_pagamento, detalhe_pagamento, total, observacoes)
                 VALUES (:cliente_id, :funcionario_id, :tipo_pagamento, :detalhe_pagamento, :total, :observacoes)'
            );
            $stmtVenda->execute([
                'cliente_id'        => $clienteId,
                'funcionario_id'    => $funcionarioId,
                'tipo_pagamento'    => $tipoPagamento,
                'detalhe_pagamento' => $detalhePagamento,
                'total'             => number_format($total, 2, '.', ''),
                'observacoes'       => $observacoes ?: null,
            ]);
            $vendaId = (int) $this->pdo->lastInsertId();

            $stmtItem = $this->pdo->prepare(
                'INSERT INTO vendas_itens (venda_id, peca_id, quantidade, preco_unitario)
                 VALUES (:venda_id, :peca_id, :quantidade, :preco_unitario)'
            );
            $stmtStock = $this->pdo->prepare('UPDATE pecas SET stock = stock - :qtd WHERE id = :id');

            foreach ($linhas as $linha) {
                $stmtItem->execute([
                    'venda_id'       => $vendaId,
                    'peca_id'        => $linha['peca']['id'],
                    'quantidade'     => $linha['quantidade'],
                    'preco_unitario' => $linha['peca']['preco'],
                ]);
                $stmtStock->execute(['qtd' => $linha['quantidade'], 'id' => $linha['peca']['id']]);
            }

            $this->pdo->commit();
            return $vendaId;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function listar(int $limite = 50): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT v.*, c.nome AS cliente, f.nome AS funcionario,
                    COUNT(i.id) AS num_itens, COALESCE(SUM(i.quantidade), 0) AS unidades
               FROM vendas v
               JOIN clientes c ON c.id = v.cliente_id
               LEFT JOIN funcionarios f ON f.id = v.funcionario_id
               LEFT JOIN vendas_itens i ON i.venda_id = v.id
              GROUP BY v.id
              ORDER BY v.data_venda DESC, v.id DESC
              LIMIT :limite'
        );
        $stmt->bindValue('limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obter(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT v.*, c.nome AS cliente, c.nif AS cliente_nif, c.email AS cliente_email, f.nome AS funcionario
               FROM vendas v
               JOIN clientes c ON c.id = v.cliente_id
               LEFT JOIN funcionarios f ON f.id = v.funcionario_id
              WHERE v.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $venda = $stmt->fetch();
        if (!$venda) {
            return null;
        }

        $stmtItens = $this->pdo->prepare(
            'SELECT i.*, p.nome AS peca, p.referencia, (i.quantidade * i.preco_unitario) AS subtotal
               FROM vendas_itens i JOIN pecas p ON p.id = i.peca_id
              WHERE i.venda_id = :id ORDER BY i.id'
        );
        $stmtItens->execute(['id' => $id]);
        $venda['itens'] = $stmtItens->fetchAll();
        return $venda;
    }

    /** Indicadores simples para o painel inicial. */
    public function resumo(): array
    {
        return $this->pdo->query(
            'SELECT COUNT(*) AS num_vendas,
                    COALESCE(SUM(total), 0) AS faturacao,
                    COALESCE(SUM(CASE WHEN DATE(data_venda) = CURDATE() THEN total END), 0) AS faturacao_hoje
               FROM vendas'
        )->fetch();
    }
}

```

---

## php/src/bootstrap.php

```php
<?php
/**
 * Ficheiro incluído no topo de todas as páginas em php/public/.
 * Trata de: configuração, autoload das classes, sessão e funções auxiliares.
 */
declare(strict_types=1);

mb_internal_encoding('UTF-8');
date_default_timezone_set('Europe/Lisbon');
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Configuração (.env) disponível globalmente
$config = require dirname(__DIR__) . '/config/env.php';

// Autoload PSR-4 simples: AutoLux\Repositorios\PecaRepository -> src/Repositorios/PecaRepository.php
spl_autoload_register(function (string $classe): void {
    $prefixo = 'AutoLux\\';
    if (!str_starts_with($classe, $prefixo)) {
        return;
    }
    $relativo = str_replace('\\', '/', substr($classe, strlen($prefixo)));
    $ficheiro = __DIR__ . '/' . $relativo . '.php';
    if (is_file($ficheiro)) {
        require $ficheiro;
    }
});

// Registo dos métodos de pagamento (ver config/pagamentos.php)
$pagamentos = new AutoLux\Pagamentos\RegistoPagamentos(require dirname(__DIR__) . '/config/pagamentos.php');

// Cliente da API Node.js
$apiFornecedores = new AutoLux\Api\FornecedoresApiClient($config['API_BASE_URL']);

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params(['httponly' => true, 'samesite' => 'Lax']);
    session_start();
}

// ---------------------------------------------------------------------------
// Funções auxiliares usadas nas páginas/templates
// ---------------------------------------------------------------------------

/** Escapa texto para HTML (evita XSS). Usar SEMPRE ao imprimir dados. */
function e(mixed $valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function formatarPreco(float|string|null $valor): string
{
    return number_format((float) $valor, 2, ',', '.') . ' €';
}

function formatarData(?string $data, string $formato = 'd/m/Y H:i'): string
{
    if (!$data) {
        return '-';
    }
    return (new DateTimeImmutable($data))->format($formato);
}

/** Guarda uma mensagem para mostrar na próxima página (padrão "flash"). */
function flash(string $tipo, string $mensagem): void
{
    $_SESSION['flash'][] = ['tipo' => $tipo, 'mensagem' => $mensagem];
}

/** Devolve e limpa as mensagens flash. */
function obterFlash(): array
{
    $mensagens = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $mensagens;
}

function redirecionar(string $url): never
{
    header('Location: ' . $url);
    exit;
}

/** Valor antigo de um campo de formulário (para repor após erro de validação). */
function antigo(string $campo, mixed $defeito = ''): string
{
    return e($_POST[$campo] ?? $defeito);
}

/** Etiqueta (badge) de estado de encomenda com classe CSS. */
function badgeEstado(string $estado): string
{
    $classes = ['pendente' => 'aviso', 'enviada' => 'info', 'recebida' => 'sucesso', 'cancelada' => 'erro'];
    return '<span class="badge badge-' . ($classes[$estado] ?? 'neutro') . '">' . e($estado) . '</span>';
}

// ---------------------------------------------------------------------------
// Proteção CSRF: cada formulário POST inclui um token secreto da sessão
// (impresso com a função campoCsrf()) que é verificado aqui em todos os pedidos POST.
// ---------------------------------------------------------------------------

function tokenCsrf(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

/** Campo escondido a colocar dentro de cada <form method="post">. */
function campoCsrf(): string
{
    return '<input type="hidden" name="csrf_token" value="' . e(tokenCsrf()) . '">';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'
    && !hash_equals(tokenCsrf(), (string) ($_POST['csrf_token'] ?? ''))) {
    http_response_code(403);
    flash('erro', 'Pedido inválido ou sessão expirada (token CSRF). Volte a submeter o formulário.');
    redirecionar(basename($_SERVER['SCRIPT_NAME']));
}

// ---------------------------------------------------------------------------
// Autenticação: todas as páginas exigem sessão iniciada, exceto o login.
// ---------------------------------------------------------------------------

$paginasPublicas = ['login.php'];
$utilizadorAtual = null;
try {
    if (!in_array(basename($_SERVER['SCRIPT_NAME']), $paginasPublicas, true)) {
        AutoLux\Auth::exigirAutenticacao();
    }
    $utilizadorAtual = AutoLux\Auth::utilizador();
} catch (Throwable $excecao) {
    // Sem base de dados não há como validar a sessão: mostra a página de erro
    $titulo = 'Base de dados indisponível';
    require dirname(__DIR__) . '/templates/erro.php';
    exit;
}

```

---

## php/templates/cabecalho.php

```php
<?php
/**
 * Cabeçalho comum a todas as páginas. Espera a variável $titulo definida
 * pela página que o inclui. $paginaAtual é usada para marcar o menu ativo.
 */
$paginaAtual = basename($_SERVER['SCRIPT_NAME'], '.php');
$menu = [
    'index'                  => ['Painel',            'index.php'],
    'pecas'                  => ['Catálogo de peças', 'pecas.php'],
    'clientes'               => ['Clientes',          'clientes.php'],
    'venda'                  => ['Nova venda',        'venda.php'],
    'vendas'                 => ['Vendas',            'vendas.php'],
    'fornecedores'           => ['Fornecedores',      'fornecedores.php'],
    'encomendas_fornecedor'  => ['Encomendas',        'encomendas_fornecedor.php'],
];
if (AutoLux\Auth::ehAdmin()) {
    $menu['funcionarios'] = ['Funcionários', 'funcionarios.php'];
}
$ativos = [
    'venda_detalhe'        => 'vendas',
    'encomenda_fornecedor' => 'encomendas_fornecedor',
];
$paginaAtiva = $ativos[$paginaAtual] ?? $paginaAtual;
$utilizadorAtual = $utilizadorAtual ?? AutoLux\Auth::utilizador();
?>
<!doctype html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($titulo ?? 'AutoLux') ?> | AutoLux Gestão</title>
  <link rel="stylesheet" href="assets/estilos.css">
</head>
<body>
<header class="topo">
  <a class="marca" href="index.php"><span class="marca-simbolo">AL</span> AutoLux <small>Armazém de peças</small></a>
  <nav class="menu" aria-label="Menu principal">
    <?php foreach ($menu as $chave => [$rotulo, $url]): ?>
      <a href="<?= $url ?>" class="<?= $paginaAtiva === $chave ? 'ativo' : '' ?>"><?= $rotulo ?></a>
    <?php endforeach; ?>
  </nav>
  <?php if ($utilizadorAtual): ?>
  <div class="sessao">
    <span class="sessao-nome" title="<?= e($utilizadorAtual['email']) ?>">
      <?= e($utilizadorAtual['nome']) ?>
      <small><?= $utilizadorAtual['perfil'] === 'admin' ? 'administrador' : 'funcionário' ?></small>
    </span>
    <form method="post" action="logout.php">
      <?= campoCsrf() ?>
      <button class="botao botao-pequeno botao-secundario" type="submit">Sair</button>
    </form>
  </div>
  <?php endif; ?>
</header>

<main class="conteudo">
  <?php foreach (obterFlash() as $f): ?>
    <div class="alerta alerta-<?= e($f['tipo']) ?>"><?= e($f['mensagem']) ?></div>
  <?php endforeach; ?>

```

---

## php/templates/erro.php

```php
<?php
/**
 * Página de erro amigável. Recebe $excecao (Throwable) e $titulo.
 * Usada pelas páginas quando a BD ou a API não estão disponíveis.
 */
require __DIR__ . '/cabecalho.php';
?>
<section class="cartao cartao-erro">
  <p class="rotulo">Ocorreu um problema</p>
  <h1><?= e($titulo) ?></h1>
  <p><?= e($excecao->getMessage()) ?></p>
  <details>
    <summary>Detalhes técnicos</summary>
    <pre><?= e(get_class($excecao)) ?> em <?= e($excecao->getFile()) ?>:<?= $excecao->getLine() ?></pre>
  </details>
  <a class="botao botao-secundario" href="index.php">Voltar ao painel</a>
</section>
<?php require __DIR__ . '/rodape.php'; ?>

```

---

## php/templates/rodape.php

```php
</main>

<footer class="rodape">
  <div>
    <strong>AutoLux</strong> · Projeto final de Backend · PHP <?= PHP_VERSION ?> + MySQL + Node.js
  </div>
  <div class="rodape-servicos">
    BD1 (vendas): <code><?= e($GLOBALS['config']['DB1_NAME']) ?></code> ·
    API fornecedores: <code><?= e($GLOBALS['config']['API_BASE_URL']) ?></code>
  </div>
</footer>
<script src="assets/app.js"></script>
</body>
</html>

```

---

## scripts/setup-db.js

```javascript
/**
 * Cria as duas bases de dados (BD1 vendas e BD2 fornecedores) e carrega
 * os dados de exemplo, executando os ficheiros .sql da pasta database/.
 *
 * Uso:  npm run db:setup
 *
 * Alternativa manual (cliente mysql):
 *   mysql -u root -p < database/bd1_vendas.sql
 *   mysql -u root -p < database/bd2_fornecedores.sql
 */
require('dotenv').config({ path: require('path').join(__dirname, '..', '.env') });

const fs = require('fs');
const path = require('path');
const mysql = require('mysql2/promise');

const ficheiros = ['bd1_vendas.sql', 'bd2_fornecedores.sql'];

async function main() {
  const ligacao = await mysql.createConnection({
    host: process.env.DB_HOST || '127.0.0.1',
    port: Number(process.env.DB_PORT || 3306),
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    multipleStatements: true,
  });

  try {
    for (const nome of ficheiros) {
      const caminho = path.join(__dirname, '..', 'database', nome);
      const sql = fs.readFileSync(caminho, 'utf8');
      process.stdout.write(`A executar ${nome}... `);
      await ligacao.query(sql);
      console.log('OK');
    }
    console.log('\nBases de dados criadas com sucesso:');
    console.log(`  - ${process.env.DB1_NAME || 'autolux_vendas'} (clientes, peças, vendas)`);
    console.log(`  - ${process.env.DB2_NAME || 'autolux_fornecedores'} (fornecedores, encomendas)`);
  } finally {
    await ligacao.end();
  }
}

main().catch((erro) => {
  console.error('\nERRO ao criar as bases de dados:', erro.message);
  console.error('Verifique as credenciais no ficheiro .env (DB_HOST, DB_USER, DB_PASSWORD).');
  process.exit(1);
});

```

---

## scripts/test-api.js

```javascript
/**
 * Teste rápido da Web API Node.js (a API tem de estar a correr: npm run start:api).
 * Percorre o ciclo completo: health -> fornecedores -> criar encomenda ->
 * consultar -> alterar estado -> validação de erro.
 *
 * Uso: npm run test:api
 */
require('dotenv').config({ path: require('path').join(__dirname, '..', '.env') });

const base = process.env.API_BASE_URL || 'http://localhost:3000/api';
let falhas = 0;

async function pedido(metodo, caminho, corpo) {
  const resposta = await fetch(base + caminho, {
    method: metodo,
    headers: { 'Content-Type': 'application/json' },
    body: corpo ? JSON.stringify(corpo) : undefined,
  });
  return { status: resposta.status, dados: await resposta.json() };
}

function verificar(descricao, condicao, extra = '') {
  console.log(`${condicao ? 'OK  ' : 'FALHA'} ${descricao}${extra ? ' — ' + extra : ''}`);
  if (!condicao) falhas++;
}

(async () => {
  console.log(`A testar ${base}\n`);

  const health = await pedido('GET', '/health');
  verificar('GET /health responde ok', health.status === 200 && health.dados.estado === 'ok');

  const fornecedores = await pedido('GET', '/fornecedores');
  verificar('GET /fornecedores devolve lista', fornecedores.status === 200 && Array.isArray(fornecedores.dados) && fornecedores.dados.length > 0,
    `${fornecedores.dados.length} fornecedores`);

  const fornecedorId = fornecedores.dados[0].id;
  const criada = await pedido('POST', '/encomendas', {
    fornecedor_id: fornecedorId,
    observacoes: 'Encomenda de teste (scripts/test-api.js)',
    criado_por: 'Script de teste',
    itens: [
      { referencia_peca: 'IG-BKR6E', descricao: 'Vela de ignição', quantidade: 100, preco_unitario: 2.9 },
      { referencia_peca: 'FL-C30135', descricao: 'Filtro de ar', quantidade: 20, preco_unitario: 9.1 },
    ],
  });
  verificar('POST /encomendas cria encomenda (201)', criada.status === 201 && criada.dados.id > 0, `id ${criada.dados.id}`);
  verificar('total calculado no servidor', Math.abs(criada.dados.total - (100 * 2.9 + 20 * 9.1)) < 0.001, `total ${criada.dados.total}`);

  const detalhe = await pedido('GET', `/encomendas/${criada.dados.id}`);
  verificar('GET /encomendas/:id devolve itens', detalhe.status === 200 && detalhe.dados.itens.length === 2);
  verificar('criado_por guardado na encomenda', detalhe.dados.criado_por === 'Script de teste');

  const estado = await pedido('PATCH', `/encomendas/${criada.dados.id}/estado`, { estado: 'enviada' });
  verificar('PATCH /encomendas/:id/estado altera estado', estado.status === 200 && estado.dados.estado === 'enviada');

  const invalida = await pedido('POST', '/encomendas', { fornecedor_id: fornecedorId, itens: [] });
  verificar('POST /encomendas sem itens devolve 400', invalida.status === 400, invalida.dados.erro);

  const inexistente = await pedido('GET', '/encomendas/999999');
  verificar('GET /encomendas/999999 devolve 404', inexistente.status === 404);

  console.log(`\n${falhas === 0 ? 'Todos os testes passaram.' : falhas + ' teste(s) falharam.'}`);
  process.exit(falhas === 0 ? 0 : 1);
})().catch((erro) => {
  console.error('Não foi possível contactar a API:', erro.message);
  console.error('Arranque-a primeiro com: npm run start:api');
  process.exit(1);
});

```

