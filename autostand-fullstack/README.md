# AutoStand Full Stack - Projeto de revisao de Back End

Este projeto foi criado para aplicar, numa unica aplicacao, a materia estudada em Back End:

- Node.js
- Express.js
- API REST
- JSON
- MySQL
- mysql2/promise
- CRUD
- Programacao Orientada a Objetos
- Heranca e polimorfismo
- Classe base "abstrata" em JavaScript
- Middleware
- JWT
- Roles admin/viewer
- bcrypt para passwords
- .env
- fs para logs
- Jest
- HTML + CSS + Bootstrap + JavaScript
- fetch() para ligar Front End e Back End
- Cookies HttpOnly (sessao JWT)
- Helmet (cabecalhos de seguranca / CSP)
- Rate limiting (express-rate-limit)
- Paginacao nas listagens da API

## 1. Estrutura

```text
autostand-fullstack/
|
|-- app.js
|-- server.js
|-- package.json
|-- .env.example
|-- .gitignore
|
|-- config/
|   `-- database.js
|
|-- models/
|   |-- Veiculo.js
|   |-- Carro.js
|   |-- Moto.js
|   `-- User.js
|
|-- repositories/
|   |-- BaseRepository.js
|   |-- veiculoRepository.js
|   |-- userRepository.js
|   `-- vendaRepository.js
|
|-- controllers/
|   |-- authController.js
|   |-- veiculoController.js
|   |-- userController.js
|   `-- vendaController.js
|
|-- routes/
|   |-- authRoutes.js
|   |-- veiculoRoutes.js
|   |-- userRoutes.js
|   `-- vendaRoutes.js
|
|-- middleware/
|   |-- authMiddleware.js
|   |-- roleMiddleware.js
|   |-- validationMiddleware.js
|   |-- requestLogger.js
|   |-- rateLimiters.js
|   `-- errorMiddleware.js
|
|-- services/
|   `-- authService.js
|
|-- utils/
|   |-- AppError.js
|   |-- logger.js
|   |-- pagination.js
|   `-- authCookie.js
|
|-- scripts/
|   `-- seed.js
|
|-- tests/
|   |-- veiculo.test.js
|   `-- auth.test.js
|
|-- sql/
|   `-- database.sql
|
|-- logs/
|   `-- .gitkeep
|
`-- public/
    |-- index.html
    |-- login.html
    |-- admin.html
    |-- css/
    |   `-- style.css
    `-- js/
        |-- api.js
        |-- app.js
        |-- login.js
        `-- admin.js
```

## 2. Mapa mental

```text
Browser
  |
  | fetch() + HTTP + JSON
  v
Express routes
  v
Middleware
  v
Controller
  v
Repository
  v
mysql2
  v
MySQL
```

Quando a resposta volta:

```text
MySQL
  v
Repository
  v
Controller
  v
res.json()
  v
Browser
```

## 3. Instalar

### Passo A - MySQL

Liga o MySQL no MAMP.

No phpMyAdmin abre o ficheiro:

```text
sql/database.sql
```

e executa o SQL.

Isto cria:

```text
autostand_db
|-- users
|-- veiculos
`-- vendas
```

### Passo B - .env

Na raiz do projeto copia:

```text
.env.example
```

para:

```text
.env
```

Configuracao frequente no MAMP para Mac:

```env
PORT=3000
DB_HOST=127.0.0.1
DB_PORT=8889
DB_USER=root
DB_PASSWORD=root
DB_NAME=autostand_db
JWT_SECRET=trocar_por_uma_chave_longa_e_aleatoria
JWT_EXPIRES_IN=1h
NODE_ENV=development
```

O servidor **nao arranca** sem `JWT_SECRET`.

Se o teu MySQL usar 3306 ou password vazia, altera apenas esses valores.

### Passo C - instalar pacotes

No Terminal, dentro da pasta do projeto:

```bash
npm install
```

### Passo D - criar users de teste

```bash
npm run seed
```

Sao criados:

```text
ADMIN
admin@autostand.pt
admin123

VIEWER
viewer@autostand.pt
viewer123
```

### Passo E - arrancar

```bash
npm run dev
```

ou:

```bash
npm start
```

Abrir:

```text
http://localhost:3000
```

## 4. API REST

### Auth

```text
POST /api/auth/login
POST /api/auth/logout
GET  /api/auth/me
```

O login grava o JWT num cookie `autostand_token` (HttpOnly, SameSite=Lax).
O browser envia o cookie sozinho. No Postman podes usar o cookie jar **ou**
continuar a enviar `Authorization: Bearer ...`.

### Veiculos

```text
GET    /api/veiculos
GET    /api/veiculos/:id
POST   /api/veiculos
PUT    /api/veiculos/:id
DELETE /api/veiculos/:id
```

GET e publico. POST/PUT/DELETE exigem sessao de admin.

Filtros e paginacao:

```text
/api/veiculos?q=BMW
/api/veiculos?tipo=carro
/api/veiculos?combustivel=hibrido
/api/veiculos?estado=disponivel
/api/veiculos?excludeEstado=vendido
/api/veiculos?ano=2024
/api/veiculos?precoMin=10000&precoMax=50000
/api/veiculos?page=1&limit=9
```

A resposta de listagens inclui `total`, `page`, `limit`, `totalPages`,
`hasNextPage`, `hasPrevPage` e `data`.

### Users

```text
GET    /api/users
POST   /api/users
DELETE /api/users/:id
```

Apenas admin.

### Vendas

```text
GET  /api/vendas
POST /api/vendas
```

GET aceita admin e viewer. POST exige admin.

## 5. Exemplos para Postman / Insomnia

### Login

POST `/api/auth/login`

```json
{
  "email": "admin@autostand.pt",
  "password": "admin123"
}
```

A resposta **nao** inclui o token no JSON. Vem um `Set-Cookie` HttpOnly.

No Postman: activa o cookie jar, faz login, e os pedidos seguintes
enviam o cookie. Alternativa (API / mobile):

```text
Authorization: Bearer O_TEU_TOKEN
```

O token so aparece se o login for feito sem cookie (por exemplo a ler
o header `Set-Cookie` ou a usar a API com Bearer gerado noutro cliente).
Para testar Bearer, podes copiar o valor do cookie `autostand_token`.

### Criar carro

POST `/api/veiculos`

```json
{
  "tipo": "carro",
  "marca": "Mercedes",
  "modelo": "C200",
  "ano": 2025,
  "preco": 52900,
  "combustivel": "diesel",
  "quilometragem": 1200,
  "portas": 4,
  "cilindradas": null,
  "estado": "disponivel"
}
```

### Criar moto

```json
{
  "tipo": "moto",
  "marca": "Ducati",
  "modelo": "Monster",
  "ano": 2024,
  "preco": 14900,
  "combustivel": "gasolina",
  "quilometragem": 850,
  "portas": null,
  "cilindradas": 937,
  "estado": "disponivel"
}
```

### Registar venda

POST `/api/vendas`

```json
{
  "vehicleId": 1,
  "customerName": "Cliente Teste",
  "customerEmail": "cliente@example.com",
  "salePrice": 87000
}
```

A operacao e feita numa transacao MySQL. Se correr bem, a venda e criada e o veiculo passa a `vendido`.

## 6. Onde esta cada materia

### Node.js

`server.js` e todo o Back End correm com Node.js.

### Express

`app.js`, `routes/`, `controllers/` e `middleware/`.

### JSON

`express.json()` recebe JSON e `res.json()` envia JSON. O JSON e o formato de comunicacao entre Front End e API.

### MySQL

`sql/database.sql` cria as tabelas. `config/database.js` cria o pool de ligacoes.

### CRUD

Em `veiculoRepository.js`:

- CREATE -> insert()
- READ -> getAll() e findById()
- UPDATE -> update()
- DELETE -> delete()

### POO

`Veiculo` e a classe base. `Carro` e `Moto` herdam de `Veiculo`.

JavaScript nao tem `abstract class` nativa como PHP. O projeto simula a abstracao com `new.target` e metodos que lancam erro quando nao sao implementados.

### Polimorfismo

Carro e Moto implementam os mesmos metodos:

```text
getTipo()
getInfoEspecifica()
```

mas devolvem resultados diferentes.

### Repository

`BaseRepository` concentra operacoes comuns de base de dados. Os repositories concretos herdam dessa base.

### JWT

`authService.js` gera o token. `utils/authCookie.js` mete-o num cookie HttpOnly.
`authMiddleware.js` le o cookie (ou o header Bearer, para o Postman).

### Roles

`roleMiddleware.js` permite limitar operacoes a `admin` ou `viewer`.

### bcrypt

Passwords sao transformadas em hash antes de serem guardadas. O login usa `bcrypt.compare()`.

### Helmet

`app.js` usa Helmet para cabecalhos HTTP (CSP, clickjacking, MIME sniffing).

### Rate limiting

`middleware/rateLimiters.js`: 200 pedidos / 15 min na API e 10 logins falhados / 15 min.

### Paginacao

`utils/pagination.js` interpreta `page` e `limit`. Os GET de veiculos, vendas e users devolvem `totalPages`.

### fs

`utils/logger.js` grava pedidos em:

```text
logs/app.log
```

### Jest

```bash
npm test
```

Testa heranca, abstracao simulada, polimorfismo, JWT e paginacao sem depender da base de dados.

## 7. Ordem recomendada para estudar o codigo

Nao tentes aprender todos os ficheiros ao mesmo tempo. Usa esta ordem:

```text
1. sql/database.sql
2. config/database.js
3. models/Veiculo.js
4. models/Carro.js
5. models/Moto.js
6. repositories/BaseRepository.js
7. repositories/veiculoRepository.js
8. controllers/veiculoController.js
9. routes/veiculoRoutes.js
10. app.js e server.js
11. testar GET no Postman
12. testar POST/PUT/DELETE
13. services/authService.js
14. utils/authCookie.js + middleware/authMiddleware.js
15. middleware/roleMiddleware.js
16. middleware/rateLimiters.js + Helmet em app.js
17. utils/pagination.js
18. public/index.html + public/js/app.js
19. public/login.html + login.js
20. public/admin.html + admin.js
21. utils/logger.js
22. tests/
```

## 8. Diferenca PHP/PDO e Node/mysql2

```text
PHP                    Node.js
-------------------    -------------------
PDO                    mysql2
$pdo->prepare()         pool.execute()
$pdo->fetchAll()        rows
parent::__construct()   super()
abstract class          simulacao em JS
$_POST / php://input    req.body
json_encode()           res.json()
```

Os conceitos sao muito semelhantes. O que muda e a linguagem e a framework.

## 9. Nota de seguranca

O JWT **nao** vai para o `localStorage`. Fica num cookie `HttpOnly` + `SameSite=Lax`,
para o JavaScript da pagina nao o conseguir ler (protege contra XSS que roubava o token).

O Helmet define uma Content-Security-Policy: scripts so do proprio site, CSS do site
e do CDN do Bootstrap.

O rate limiter no login reduz tentativas de adivinhar a password.

Isto continua a ser um projeto de curso. Em producao haveria ainda HTTPS obrigatorio,
cookies `Secure`, CSRF token se o SameSite nao chegasse, e rate limiting por conta
(nao so por IP).
