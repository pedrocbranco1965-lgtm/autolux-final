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
