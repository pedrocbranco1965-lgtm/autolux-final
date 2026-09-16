AUTOLUX — PROJETO FINAL DE BACKEND
===================================

Aluno: Pedro Castel-Branco
Fonte de dados utilizada: C — bases de dados MySQL locais com dados de demonstração.

DESCRIÇÃO
---------
Aplicação web para a gestão de um armazém de peças automóveis. A interface e o
backend de vendas são desenvolvidos em PHP. A gestão de fornecedores é feita
através de uma API REST Node.js. Cada componente utiliza a sua própria base de
dados MySQL, conforme o enunciado.

FUNCIONALIDADES
---------------
- Painel com totais de peças, stock, clientes e vendas.
- Catálogo de peças obtido do MySQL.
- Filtros por marca, tipo, preço máximo e pesquisa por nome/SKU.
- Lista de clientes e resumo das respetivas compras.
- Formulário de venda com dropdown de clientes, peça, quantidade e pagamento.
- Métodos de pagamento guardados numa tabela para permitir adicionar novos.
- Registo transacional de vendas e redução automática do stock.
- Histórico de vendas.
- Lista de fornecedores recebida da API Node.js.
- Submissão de encomendas a fornecedores através da API REST.
- Validação de dados, controlo de stock e transações na API.
- Interface responsiva e proteção CSRF nos formulários.

ARQUITETURA
-----------

Navegador
   |
   v
Interface PHP (porta 8080) --------> Base 1: autolux_vendas
   |
   | HTTP/JSON
   v
API Node.js (porta 3000) ----------> Base 2: autolux_fornecedores

Estrutura:
- php-app/public: páginas PHP e estilos.
- php-app/src: configuração, funções e elementos comuns.
- api/src: API REST Node.js/Express.
- api/test: testes automáticos da validação.
- database/sales/init.sql: clientes, peças, pagamentos e vendas.
- database/suppliers/init.sql: fornecedores, peças e encomendas.
- docker-compose.yml: arranque dos quatro serviços.

COMO EXECUTAR (MÉTODO RECOMENDADO)
---------------------------------
Pré-requisitos: Docker Desktop (Windows/macOS) ou Docker Engine com Compose.

1. Abrir um terminal na pasta do projeto.
2. Criar a configuração local:
   cp .env.example .env

   No Windows PowerShell:
   Copy-Item .env.example .env

3. Construir e iniciar:
   docker compose up --build

4. Esperar até as duas bases de dados indicarem que estão prontas.
5. Abrir:
   http://localhost:8080

A API pode ser consultada em:
- http://localhost:3000/api/health
- http://localhost:3000/api/suppliers
- http://localhost:3000/api/orders

Para parar:
   docker compose down

Para apagar as bases e voltar aos dados iniciais:
   docker compose down -v
   docker compose up --build

EXECUTAR A API NODE.JS SEM DOCKER
--------------------------------
É necessário ter MySQL e executar primeiro database/suppliers/init.sql.

1. Instalar:
   npm install

2. Definir as variáveis DB_HOST, DB_PORT, DB_NAME, DB_USER e DB_PASSWORD.
3. Iniciar:
   npm start

Por omissão, a API procura MySQL em localhost:3308 e fica na porta 3000.

TESTES
------
   npm test

NOTA SOBRE OS DADOS
-------------------
Os nomes, emails e peças incluídos nos ficheiros SQL são dados fictícios para
demonstração. Se a designação A/B/C usada pelo professor atribuir outra letra
a dados MySQL locais, alterar apenas a linha "Fonte de dados utilizada" acima.

COMO ADICIONAR UM MÉTODO DE PAGAMENTO
-------------------------------------
Não é preciso alterar a página PHP. Basta inserir um registo na Base 1:

INSERT INTO payment_methods (code, name)
VALUES ('cheque', 'Cheque');

O novo método passa automaticamente a aparecer no formulário de venda.
