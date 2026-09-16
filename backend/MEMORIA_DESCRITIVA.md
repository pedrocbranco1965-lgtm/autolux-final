# Memória descritiva — AutoLux (Projeto Final de Backend)

## 1. Descrição do produto

A AutoLux é um armazém de peças automóveis. Esta solução é a ferramenta interna
usada pelos **funcionários do armazém** (balcão e compras) para:

- consultar o catálogo de peças e o respetivo stock;
- consultar a carteira de clientes;
- registar encomendas de peças para clientes (vendas), com pagamento;
- consultar fornecedores e submeter-lhes encomendas de reposição.

Não é uma loja online: todas as páginas assumem um funcionário a operar o
sistema a partir da rede interna do armazém.

## 2. Arquitetura

A solução segue exatamente as três camadas do enunciado:

```
                       ┌──────────────────────────────┐
                       │   Páginas dinâmicas PHP       │   Apresentação
                       │   (interface de gestão)       │
                       └───────┬──────────────┬────────┘
                               │              │ HTTP/JSON
                       PDO     │              │
                               ▼              ▼
              ┌────────────────────┐   ┌──────────────────────────┐
              │ Backend de vendas   │   │ Serviço de compras       │  Serviços
              │ (PHP)               │   │ (Node.js + Express)      │
              └─────────┬───────────┘   └────────────┬─────────────┘
                        │                            │
                        ▼                            ▼
              ┌────────────────────┐   ┌──────────────────────────┐
              │ MySQL - BD 1        │   │ MySQL - BD 2             │  Dados
              │ clientes, material, │   │ fornecedores,            │
              │ vendas              │   │ encomendas               │
              └────────────────────┘   └──────────────────────────┘
```

Regra fundamental respeitada em todo o projeto: **o PHP nunca liga à Base de
Dados 2**. Fornecedores e encomendas são sempre obtidos e submetidos através da
web API Node.js, que é a única com credenciais para essa base de dados. Isto
mantém o módulo de compras isolado e permitiria, por exemplo, mudar a tecnologia
de armazenamento das compras sem tocar numa única página PHP.

## 3. Opções técnicas

### 3.1 Camada PHP

- **PHP 8.1+ sem framework**, com autoloading PSR-4 próprio (`bootstrap.php`).
  A escolha é deliberada: o enunciado pede "páginas dinâmicas em PHP" e um
  framework esconderia precisamente aquilo que se pretende demonstrar.
- **Organização em camadas**: as páginas em `public/` só tratam do pedido HTTP,
  delegam as regras em `src/Service/`, que por sua vez usam `src/Repository/`
  para falar com a base de dados; o HTML vive em `views/`. Nenhuma página
  contém SQL.
- **PDO com prepared statements** em todas as consultas, `ERRMODE_EXCEPTION` e
  emulação desativada.
- **Transações** no registo de vendas: cabeçalho, linhas e abate de stock são
  gravados em bloco. O abate usa `UPDATE ... WHERE stock >= :quantidade`, pelo
  que duas vendas simultâneas da mesma peça nunca produzem stock negativo.
- **Segurança**: token CSRF em todos os formulários, escape de todo o output com
  `htmlspecialchars`, e validação feita sempre no servidor (o preço usado é o da
  base de dados, nunca o que vem do browser).

### 3.2 Métodos de pagamento (padrão Strategy + Factory)

O ponto do enunciado que exige "extensibilidade fácil para introdução de novos"
tipos de pagamento é resolvido com:

- a tabela `metodos_pagamento`, que alimenta o dropdown da página;
- a interface `App\Payment\MetodoPagamento`, implementada por uma classe por
  método (`PagamentoMbWay`, `PagamentoMultibanco`, `PagamentoCartao`, ...);
- a fábrica `RegistoMetodosPagamento`, que converte a linha da base de dados na
  classe correspondente e recorre a `PagamentoGenerico` quando não existe classe
  dedicada.

Cada método declara os seus próprios campos de formulário, as suas validações e
o estado em que a venda fica (paga ou pendente) com a respetiva referência.
Acrescentar o "PayPal" é um `INSERT` — e, se precisar de lógica própria, uma
classe nova. **Nenhuma página PHP é alterada em qualquer dos casos.**

### 3.3 Serviço Node.js

- **Express 4 + mysql2/promise**, com código totalmente assíncrono
  (`async`/`await`), sem callbacks nem chamadas bloqueantes.
- **Separação em rotas → controladores → serviços → repositórios**: as rotas só
  mapeiam URLs, os controladores tratam do HTTP, os serviços têm as regras de
  negócio (validações, transições de estado) e os repositórios o SQL.
- **Pool de ligações** com parâmetros nomeados; a criação de encomendas corre
  dentro de uma transação (`withTransaction`), incluindo a geração do número
  sequencial com `FOR UPDATE` para evitar números duplicados.
- **Tratamento central de erros**: `HttpError` para erros de negócio e um
  middleware que traduz os códigos do MySQL (`ER_DUP_ENTRY`, `ECONNREFUSED`,
  ...) em respostas JSON compreensíveis. `asyncHandler` garante que nenhuma
  rejeição de promessa fica por tratar.
- **Regras de negócio no servidor**: os subtotais e o total são sempre
  recalculados na API, e não se aceitam encomendas a fornecedores inativos nem
  transições de estado inválidas (uma encomenda recebida não volta atrás).

### 3.4 Integração entre as camadas

- Do lado PHP, toda a comunicação passa por `App\Support\Http\ApiClient`, que
  concentra timeouts, descodificação JSON e tradução de erros em `ApiException`.
  Usa cURL e, se a extensão não existir, recorre a streams.
- As páginas apanham `ApiException` e mostram um aviso: com o serviço Node.js
  desligado, a interface de vendas continua a funcionar normalmente.
- No browser, o JavaScript nunca fala diretamente com a API Node.js: pede a
  endpoints JSON do próprio PHP (`/api/peca.php`, `/api/fornecedor-artigos.php`),
  mantendo o PHP como interface de gestão e o Node.js como camada intermédia de
  compras.

## 4. Ferramentas utilizadas

| Área | Ferramenta |
|------|-----------|
| Interface de gestão | PHP 8.3 (servidor embutido ou Apache/XAMPP) |
| Serviço de compras | Node.js 22, Express 4, mysql2, cors, dotenv |
| Bases de dados | MySQL / MariaDB 10.11 |
| Arranque e setup | Scripts Node.js (`npm start`, `npm run db:setup`) |
| Controlo de versões | Git |

O arranque foi propositadamente reduzido a três comandos (`npm install`,
`npm run db:setup`, `npm start`) para que o projeto seja executável sem
configuração manual de Apache ou importação de ficheiros SQL à mão.

## 5. Aspetos a desenvolver no futuro

- **Autenticação e perfis**: sessão de funcionário com permissões distintas para
  balcão, compras e administração.
- **Receção de encomendas ligada ao stock**: quando uma encomenda a fornecedor
  passa a "recebida", incrementar automaticamente o stock na Base de Dados 1
  (implica uma chamada da API para um endpoint do lado das vendas).
- **Integração real de pagamentos**: substituir a simulação por gateways
  verdadeiros (SIBS/MB WAY), aproveitando o facto de cada método já estar
  isolado na sua própria classe.
- **Documentação da API** com OpenAPI/Swagger e testes automatizados
  (PHPUnit do lado PHP, Jest/node:test do lado Node.js).
- **Faturação**: emissão de fatura em PDF e envio por email ao cliente.
- **Paginação e cache** no catálogo, necessárias quando o número de referências
  crescer para milhares.
