AutoLux - Projeto Final Backend (PHP + Node.js + MySQL)

Aluno: Pedro Castel-Branco

Projeto: AutoLux — armazém de peças automóveis
Funcionários consultam stock e clientes, registam vendas de peças e
encomenda novas peças aos fornecedores.

Fonte de dados utilizada: C (Base de dados MySQL)
  A = ficheiro JSON (backend/data/pecas.json)
  B = API REST (php/api_pecas.php)
  C = MySQL direto — Base de Dados 1
A fonte troca-se na variável FONTE_DADOS do ficheiro .env.
A versão entregue usa C, como pede o mapeamento técnico do enunciado.


Como correr o projeto (forma mais simples — Docker)

1. Instalar Docker Desktop
2. Na pasta backend:
   docker compose up --build
3. Abrir http://localhost:8080
   Utilizador: admin
   Palavra-passe: autolux

A API Node.js fica em http://localhost:3000
Exemplo: http://localhost:3000/api/fornecedores


Como correr o projeto (npm + PHP, sem Docker)

Precisa de: PHP 8+ com extensão pdo_mysql, Node.js 18+, MySQL/MariaDB.

1. Criar as bases de dados (no MySQL, como root):
   mysql -u root -p < database/01_autolux_vendas.sql
   mysql -u root -p < database/02_autolux_fornecedores.sql

2. Na pasta backend:
   cp .env.example .env
   npm install
   npm start

3. Noutro terminal, ainda na pasta backend:
   php -S 127.0.0.1:8080 -t php

4. Abrir http://127.0.0.1:8080
   Utilizador: admin
   Palavra-passe: autolux

Com XAMPP/WAMP: copiar a pasta php/ para htdocs/autolux, importar os SQL
no phpMyAdmin, arrancar a API com npm start, e ajustar .env
(API_FORNECEDORES_URL=http://127.0.0.1:3000).


Arquitetura (como pede o enunciado)

Camada de apresentação e vendas (PHP)
  - Páginas dinâmicas no backoffice
  - Lógica de clientes, catálogo e vendas
  - Liga-se diretamente à Base de Dados 1 (autolux_vendas)

Camada de gestão de fornecedores (Node.js)
  - API REST em Express
  - Recebe pedidos HTTP da interface PHP
  - Liga-se à Base de Dados 2 (autolux_fornecedores)

Camada de armazenamento (MySQL)
  - BD1: clientes, peças/stock, vendas, funcionários
  - BD2: fornecedores e encomendas a fornecedores


Funcionalidades implementadas

A) Catálogo de peças
  - Listagem a partir da fonte de dados escolhida
  - Filtros por marca, tipo de peça e gama de preço (mínimo e máximo)
  - Indicador de stock

B) Formulário de encomenda/venda a cliente
  - Dropdown de clientes (MySQL BD1)
  - Informação da peça (marca, tipo, referência, preço, stock, descrição)
  - Tipo de pagamento escolhido a partir de um catálogo PHP
    Métodos incluídos: Numerário, Multibanco, MB WAY, Transferência, Cartão
    Para acrescentar um método novo: criar uma classe em
    php/includes/pagamentos/metodos/ que implemente MetodoPagamento
    e adicioná-la em CatalogoPagamentos::todos(). A página não muda.
  - A venda abate stock numa transação

C) Lista de fornecedores e submissão de encomenda
  - GET /api/fornecedores e GET /api/encomendas
  - POST /api/encomendas
  - O PHP NÃO acede à BD2; só fala com a API Node.js


Endpoints da API Node.js

GET  /api/health
GET  /api/fornecedores
GET  /api/fornecedores/:id
GET  /api/encomendas
GET  /api/encomendas/:id
POST /api/encomendas
     corpo JSON:
     {
       "fornecedor_id": 1,
       "peca_referencia": "BOS-FO-001",
       "peca_nome": "Filtro de óleo P3330",
       "quantidade": 20,
       "preco_previsto": 6.20,
       "observacoes": "reposição"
     }


Estrutura

backend/
  README.txt              este ficheiro
  GUIA_ESTUDO.txt         como o projeto está pensado (para estudar / refazer)
  package.json            npm install / npm start (API Node)
  src/                    código da API
  php/                    interface e backend de vendas
  database/               scripts SQL das duas bases
  data/pecas.json         fonte A
  docker-compose.yml      arranque completo para o professor


Contas de demonstração
  admin / autolux
  funcionario / autolux
