==========================================================================
 AutoLux - Projeto Final de Backend
 Armazem de pecas automoveis: interface de gestao em PHP/MySQL
 e servico de compras em Node.js/MySQL
==========================================================================

ALUNO
-----
Pedro Castel-Branco

FONTE DE DADOS UTILIZADA
------------------------
Base de dados MySQL (opcao B - dados obtidos de base de dados relacional).

Existem duas bases de dados independentes, como pedido no enunciado:

  * autolux_vendas  (Base de Dados 1) - clientes, material/stock e vendas.
                    Acedida DIRETAMENTE pela camada PHP, atraves de PDO.

  * autolux_compras (Base de Dados 2) - fornecedores e encomendas a
                    fornecedores. Acedida EXCLUSIVAMENTE pelo servico
                    Node.js; o PHP nunca liga a esta base de dados, fala
                    sempre com a web API (HTTP/JSON).

Os scripts de criacao e os dados de exemplo estao em sql/01_bd1_vendas.sql
e sql/02_bd2_compras.sql.


--------------------------------------------------------------------------
COMO CORRER O PROJETO
--------------------------------------------------------------------------

Pre-requisitos:
  - Node.js 18 ou superior
  - PHP 8.1 ou superior (o XAMPP ja inclui; extensoes pdo_mysql e curl)
  - Servidor MySQL / MariaDB a correr (no XAMPP: iniciar o modulo MySQL)

Passos (dentro da pasta backend/):

  1) npm install          Instala as dependencias do Node.js
                          (express, mysql2, cors, dotenv).

  2) npm run db:setup     Cria as DUAS bases de dados e carrega os dados
                          de exemplo (28 pecas, 8 clientes, 8 fornecedores,
                          6 metodos de pagamento e encomendas de exemplo).

  3) npm start            Arranca a solucao completa:
                            - servico Node.js  -> http://127.0.0.1:3001/api
                            - interface PHP    -> http://127.0.0.1:8000

  4) Abrir no browser:    http://127.0.0.1:8000

Comandos auxiliares:
  npm run check           Verifica PHP, extensoes, MySQL e bases de dados.
  npm run start:api       Arranca apenas o servico Node.js.
  npm run start:web       Arranca apenas a interface PHP.

Configuracao:
  Por omissao usa MySQL em 127.0.0.1:3306 com utilizador "root" e password
  vazia (predefinicao do XAMPP). Para alterar, copiar .env.example para
  .env e editar os valores. O mesmo ficheiro .env e lido pelo PHP e pelo
  Node.js.

  Se o PHP nao estiver no PATH (Windows/XAMPP), indicar o caminho:
      set PHP_BIN=C:\xampp\php\php.exe        (Windows)
      export PHP_BIN=/opt/lampp/bin/php       (Linux/macOS)


--------------------------------------------------------------------------
FUNCIONALIDADES IMPLEMENTADAS
--------------------------------------------------------------------------

A) CATALOGO DE PECAS  (catalogo.php)
   - Listagem das pecas obtidas da Base de Dados 1 (MySQL).
   - Filtros por marca, por tipo de peca e por gama de preco (minimo e
     maximo), mais pesquisa por designacao/referencia, ordenacao e opcao
     de mostrar apenas pecas com stock.
   - Indicacao visual de stock disponivel, stock baixo e peca esgotada.

B) FORMULARIO DE ENCOMENDA DE PECA PARA CLIENTE  (nova-venda.php)
   - Dropdown com os clientes ativos da base de dados.
   - Selecao de pecas com informacao da peca (referencia, marca, tipo,
     preco e stock) carregada de forma assincrona (fetch) a partir do
     endpoint interno /api/peca.php, garantindo precos e stock atuais.
   - Possibilidade de adicionar varias pecas, com calculo de subtotais e
     total no browser.
   - Tipo de pagamento escolhido a partir dos metodos ativos na tabela
     metodos_pagamento. Cada metodo mostra os seus proprios campos
     (telemovel no MB WAY, valor entregue em numerario, IBAN na
     transferencia, prazo na conta corrente, etc.), valida-os e decide se
     a venda fica "paga" ou "pendente", gerando a respetiva referencia.
   - EXTENSIBILIDADE: acrescentar um metodo de pagamento novo e apenas um
     INSERT na tabela metodos_pagamento. Opcionalmente cria-se uma classe
     em web/src/Payment/ e regista-se em RegistoMetodosPagamento; sem ela,
     o metodo funciona atraves de PagamentoGenerico. Nenhuma pagina PHP
     precisa de ser alterada.
   - Ao registar a venda, o stock e abatido dentro de uma transacao, com
     verificacao de stock suficiente (nunca fica stock negativo).

C) FORNECEDORES E ENCOMENDAS  (fornecedores.php e encomendas.php)
   - Lista de fornecedores obtida por integracao com a web API Node.js,
     com filtros por texto, pais e apenas ativos.
   - Submissao de encomendas ao fornecedor atraves da API
     (POST /api/encomendas): o catalogo de artigos do fornecedor escolhido
     e carregado de forma assincrona e a encomenda e gravada na Base de
     Dados 2 pelo servico Node.js, dentro de uma transacao.
   - Acompanhamento do estado das encomendas (submetida -> confirmada ->
     recebida) com validacao das transicoes permitidas no servidor.
   - Pagina de detalhe de cada encomenda com as respetivas linhas.

Outras funcionalidades:
   - Painel inicial (index.php) com indicadores das duas camadas: pecas,
     valor de stock, clientes, vendas do mes, pecas abaixo do stock minimo
     e resumo das compras vindo da API Node.js.
   - Clientes (clientes.php): listagem com total comprado por cliente e
     registo de novos clientes com validacao de NIF e email.
   - Vendas (vendas.php / venda.php): historico com filtro por cliente e
     pagina de detalhe com linhas e dados do pagamento.
   - Mensagens de sucesso/erro em sessao e padrao Post/Redirect/Get.
   - Protecao contra CSRF em todos os formularios e escape de todo o
     output HTML.
   - Quando o servico Node.js esta desligado, as paginas PHP continuam a
     funcionar e mostram um aviso explicativo em vez de rebentarem.


--------------------------------------------------------------------------
ENDPOINTS DA WEB API NODE.JS
--------------------------------------------------------------------------

  GET    /api/health                      Estado do servico e da BD 2
  GET    /api/fornecedores?q=&pais=&ativos=true
  GET    /api/fornecedores/paises
  GET    /api/fornecedores/:id            Fornecedor + catalogo de artigos
  GET    /api/fornecedores/:id/artigos
  GET    /api/encomendas?fornecedorId=&estado=
  GET    /api/encomendas/resumo           Totais por estado
  GET    /api/encomendas/:id              Encomenda + linhas
  POST   /api/encomendas                  Submeter encomenda
  PATCH  /api/encomendas/:id/estado       Atualizar estado

Exemplo de submissao:

  curl -X POST http://127.0.0.1:3001/api/encomendas \
       -H "Content-Type: application/json" \
       -d '{"fornecedorId":1,"dataPrevista":"2026-10-15",
            "itens":[{"referencia":"FL-2001","designacao":"Filtro de oleo",
                      "quantidade":20,"precoUnitario":5.40}]}'


--------------------------------------------------------------------------
ESTRUTURA DO PROJETO
--------------------------------------------------------------------------

  backend/
    package.json           Dependencias e scripts (npm install / npm start)
    .env.example           Configuracao partilhada por PHP e Node.js
    sql/                   Scripts das duas bases de dados MySQL
    api/                   Servico Node.js (camada de compras)
      server.js            Arranque do servidor
      app.js               Configuracao do Express
      config/              Leitura do .env
      db/                  Pool de ligacoes e transacoes (mysql2)
      routes/              Definicao das rotas REST
      controllers/         Leitura do pedido e resposta JSON
      services/            Regras de negocio e validacoes
      repositories/        Consultas SQL a Base de Dados 2
      middleware/          Log de pedidos e tratamento de erros
      utils/               HttpError, asyncHandler e validadores
    web/                   Interface de gestao PHP (camada de vendas)
      bootstrap.php        Autoloader, sessao e tratamento de erros
      public/              Paginas dinamicas e endpoints JSON internos
      src/Support/         Config, PDO, vistas, flash, CSRF, cliente HTTP
      src/Repository/      Consultas SQL a Base de Dados 1
      src/Service/         Catalogo, vendas e integracao com a API
      src/Payment/         Metodos de pagamento (extensiveis)
      views/               Templates HTML das paginas
    scripts/               Arranque conjunto, setup da BD e verificacoes
    MEMORIA_DESCRITIVA.md  Memoria descritiva do projeto


--------------------------------------------------------------------------
NOTAS
--------------------------------------------------------------------------
- Todas as consultas usam prepared statements (PDO e mysql2), pelo que a
  aplicacao esta protegida contra SQL injection.
- O registo de vendas e a submissao de encomendas correm dentro de
  transacoes, para nao deixarem registos incompletos.
- Testado com PHP 8.3, Node.js 22 e MariaDB 10.11.
