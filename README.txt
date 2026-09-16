AutoLux - Projeto Final Backend

Aluno: [colocar o nome do aluno]

Projeto:
Solucao web para a AutoLux, um armazem de pecas automoveis. A aplicacao permite
consultar stock, filtrar pecas, registar vendas a clientes e submeter encomendas
a fornecedores atraves de uma API Node.js.

Fonte de dados utilizada:
Versao A - dados iniciais criados nos ficheiros SQL:
- mysql/vendas.sql
- mysql/fornecedores.sql

Tecnologias utilizadas:
- PHP 8.3 com Apache para a interface de gestao e backend de vendas
- MySQL 8.4 para a base de dados de clientes, pecas, stock e vendas
- Node.js com Express para a API de fornecedores
- MySQL 8.4 para a base de dados de fornecedores e encomendas
- Docker Compose para executar todos os servicos

Funcionalidades implementadas:
1. Catalogo de Pecas
   - Lista pecas guardadas em MySQL.
   - Filtros por marca, tipo de peca e preco maximo.
   - Mostra referencia, nome, marca, tipo, preco e stock.

2. Formulario de venda/encomenda de peca para cliente
   - Dropdown de clientes carregado da base de dados.
   - Dropdown de pecas com preco e stock.
   - Dropdown de tipos de pagamento vindo da tabela metodos_pagamento.
   - Registo de venda com transacao e atualizacao do stock.
   - Lista das ultimas vendas registadas.

3. Lista de fornecedores e submissao de encomenda
   - Pagina PHP consome a API Node.js por HTTP.
   - Lista fornecedores vindos da base MySQL de fornecedores.
   - Submete novas encomendas a fornecedores por POST para a API.
   - Lista encomendas guardadas pela API Node.js.

Estrutura principal:
- php/src - paginas PHP dinamicas e estilos
- php/Dockerfile - imagem PHP com extensao pdo_mysql
- fornecedores-api - API Node.js/Express
- mysql/vendas.sql - schema e dados iniciais de clientes, pecas, pagamentos e vendas
- mysql/fornecedores.sql - schema e dados iniciais de fornecedores e encomendas
- docker-compose.yml - arranca PHP, API Node.js e duas bases MySQL

Como correr o projeto completo:
1. Garantir que o Docker esta instalado.

2. Na pasta do projeto, executar:
   docker compose up --build

3. Abrir no browser:
   http://localhost:8080

4. API Node.js de fornecedores:
   http://localhost:3000/health
   http://localhost:3000/api/fornecedores
   http://localhost:3000/api/encomendas

Como correr apenas a API Node.js:
1. Instalar dependencias:
   npm install

2. Iniciar a API:
   npm start

Nota: para a API funcionar fora do Docker, e necessario ter uma base MySQL
compativel configurada e as variaveis DB_HOST, DB_NAME, DB_USER e DB_PASS.

Credenciais locais usadas pelo Docker:
- Base vendas: host localhost, porta 3307, base autolux_vendas
- Base fornecedores: host localhost, porta 3308, base autolux_fornecedores
- Utilizador: autolux
- Password: autolux123

Observacoes para estudo:
- A camada PHP liga diretamente a base de dados de vendas com PDO.
- Os metodos de pagamento sao extensíveis: para adicionar um novo metodo basta
  inserir uma linha na tabela metodos_pagamento.
- A camada PHP nao acede diretamente a base de fornecedores; comunica com a API
  Node.js, cumprindo a separacao pedida no enunciado.
