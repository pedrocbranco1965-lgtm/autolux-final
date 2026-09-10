# AutoStand Full Stack — explicação do código para estudar

Este ficheiro existe para aprenderes o teu projecto **AutoStand Full Stack**
(repositório `pedrocbranco1965-lgtm/AutoStand-Full-Stack`).

Não altera a aplicação. Só explica o código, pedido a pedido.

Como usar:

1. Abre este ficheiro num lado e o código no outro.
2. Segue a ordem das secções. Cada uma é uma camada do mesmo pedido.
3. Quando vires "em PHP seria...", é só para ligares ao módulo que acabaste.
4. No fim há perguntas para te testares sem olhar.

O exemplo que vamos seguir:

> O visitante escreve **BMW** na pesquisa e carrega em Pesquisar.

Depois fazemos o mesmo para **login**, **criar veículo** e **registar venda**.

---

## 1. O que esta aplicação faz

É um stand de carros e motos.

- Qualquer pessoa vê o catálogo (`public/index.html`).
- Quem tem conta entra em `login.html`.
- O **admin** cria/edita/apaga veículos, cria utilizadores e regista vendas.
- O **viewer** só consulta veículos e vendas.

Tecnologias (as do teu curso, em Node.js em vez de PHP):

| Matéria        | Onde está no projecto              |
|----------------|------------------------------------|
| Node.js        | `server.js`                        |
| Express        | `app.js` + `routes/`               |
| JSON           | `req.body` e `res.json()`          |
| MySQL          | `sql/database.sql` + `mysql2`      |
| CRUD           | `repositories/veiculoRepository.js`|
| POO / herança  | `models/Veiculo.js`, `Carro`, `Moto`|
| Middleware     | pasta `middleware/`                |
| JWT            | `authService.js` + `authMiddleware`|
| Roles          | `roleMiddleware.js`                |
| bcrypt         | `scripts/seed.js` e login          |
| `.env`         | `config/database.js`               |
| `fs`           | `utils/logger.js`                  |
| Jest           | pasta `tests/`                     |
| Front-end      | `public/` com `fetch()`            |

---

## 2. Mapa das pastas (lê isto primeiro)

```text
autostand-fullstack/
├── server.js              Arranca o HTTP. Não trata pedidos.
├── app.js                 Liga middlewares, rotas e o front-end.
├── package.json           Dependências e comandos npm.
├── .env                   Segredos (não vai para o Git).
│
├── public/                O que o browser vê.
│   ├── index.html         Catálogo público.
│   ├── login.html         Formulário de login.
│   ├── admin.html         Área reservada.
│   ├── css/style.css
│   └── js/
│       ├── api.js         fetch() + token JWT.
│       ├── app.js         Catálogo.
│       ├── login.js       Login.
│       └── admin.js       CRUD + vendas.
│
├── routes/                URLs da API.
├── middleware/            Código que corre ANTES do controller.
├── controllers/           Recebem o HTTP, chamam o repository.
├── services/              Regras que não são SQL (login/JWT).
├── repositories/          SQL. Únicos ficheiros que falam com o MySQL.
├── models/                Classes POO (Veiculo, Carro, Moto, User).
├── config/database.js     Pool de ligações MySQL.
├── sql/database.sql       CREATE TABLE + dados de exemplo.
├── utils/                 Logger e AppError.
├── scripts/seed.js        Cria admin e viewer com bcrypt.
└── tests/                 Jest, sem precisar da base de dados.
```

Regra de ouro do projecto:

> O controller **não** escreve SQL.
> O repository **não** envia HTTP.
> O model **não** conhece o Express.

Em PHP costumavas separar o mesmo: router → controller → classe/PDO.

---

## 3. Analogia rápida PHP → Node

```text
PHP                         Node neste projecto
----------------------      --------------------------
index.php / router          app.js + routes/
$_GET / $_POST              req.query / req.body
json_encode()               res.json()
PDO prepare/execute         pool.execute('...?', [valores])
class Veiculo (abstract)    class Veiculo + new.target
parent::__construct()       super()
password_hash()             bcrypt.hash()
sessão / cookie             JWT no localStorage
error_log()                 fs.appendFile (logger.js)
```

O conceito não muda. Muda a linguagem.

---

## 4. Arranque: o servidor liga-se ao MySQL e fica à espera

### 4.1 `package.json`

```json
"start": "node server.js",
"dev": "nodemon server.js",
"seed": "node scripts/seed.js",
"test": "jest --runInBand"
```

- `npm start` corre `server.js`.
- `npm run dev` faz o mesmo, mas o nodemon reinicia quando gravas um ficheiro.
- `npm run seed` cria `admin@autostand.pt` / `admin123`.
- `npm test` corre os testes de POO e JWT **sem** MySQL.

### 4.2 `server.js` — linha a linha

```js
require('dotenv').config();
```

Lê o ficheiro `.env` e mete os valores em `process.env`.
É aqui que entram `PORT`, `DB_HOST`, `DB_PASSWORD`, `JWT_SECRET`.
Nunca commits passwords no código.

```js
const app = require('./app');
const { testConnection } = require('./config/database');
```

Importa a app Express e a função que testa o MySQL.

```js
const PORT = Number(process.env.PORT || 3000);
```

Se o `.env` não tiver `PORT`, usa 3000. `Number(...)` garante que é um número.

```js
async function startServer() {
    try {
        await testConnection();          // 1. MySQL responde?
        app.listen(PORT, () => { ... }); // 2. Só depois abre o HTTP
    } catch (error) {
        process.exit(1);                 // 3. Se a BD falhar, o processo morre
    }
}
startServer();
```

Porquê testar a BD primeiro? Porque sem MySQL a API não serve de nada.
É melhor falhar logo no terminal do que devolver 500 em todos os pedidos.

### 4.3 `config/database.js` — o PDO deste projecto

```js
const pool = mysql.createPool({
    host: process.env.DB_HOST || '127.0.0.1',
    port: Number(process.env.DB_PORT || 3306),
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'autostand_db',
    connectionLimit: 10
});
```

Um **pool** é um conjunto de ligações reutilizáveis.
Abrir uma ligação MySQL em cada pedido seria lento.
Aqui há até 10 ligações partilhadas — ideia parecida a guardar um `$pdo` global.

```js
const [rows] = await pool.execute(sql, params);
```

`execute` envia SQL com `?`. Os valores vão em `params`, **nunca concatenados
na string**. Isto evita SQL injection. Em PHP: `$pdo->prepare()` + `execute()`.

O `mysql2/promise` devolve `[rows, fields]`. Por isso se escreve
`const [rows] = ...` — só interessam as linhas.

---

## 5. `app.js` — o router central

```js
const app = express();

app.use(express.json());
app.use(express.urlencoded({ extended: true }));
app.use(requestLogger);
```

`app.use` instala middleware: corre em **todos** os pedidos, por esta ordem.

- `express.json()` — se o body for JSON, enche `req.body`.
  Em PHP: `json_decode(file_get_contents('php://input'), true)`.
- `urlencoded` — o mesmo para formulários HTML clássicos (`$_POST`).
- `requestLogger` — no fim do pedido escreve uma linha em `logs/app.log`.

```js
app.use('/api/auth', authRoutes);
app.use('/api/veiculos', veiculoRoutes);
app.use('/api/users', userRoutes);
app.use('/api/vendas', vendaRoutes);
```

Tradução:

- `POST /api/auth/login`     → `routes/authRoutes.js`
- `GET  /api/veiculos`       → `routes/veiculoRoutes.js`
- `GET  /api/users`          → `routes/userRoutes.js`
- `POST /api/vendas`         → `routes/vendaRoutes.js`

```js
app.use(express.static(path.join(__dirname, 'public')));
```

Ficheiros de `public/` passam a URLs:

- `public/index.html`  → `http://localhost:3000/`
- `public/js/app.js`   → `http://localhost:3000/js/app.js`
- `public/css/style.css` → `http://localhost:3000/css/style.css`

O Express serve o HTML/CSS/JS **e** a API no mesmo porto 3000.
Por isso o `fetch('/api/veiculos')` funciona sem CORS: o browser
acha que está a falar com o mesmo site.

```js
app.use('/api', (req, res) => {
    res.status(404).json({ success: false, message: 'Rota da API nao encontrada.' });
});
app.use(errorMiddleware);
```

Se ninguém apanhou o URL `/api/...`, devolve JSON 404.
O `errorMiddleware` tem de ser **o último**: é o sítio único onde
os `throw new AppError` se transformam em JSON de erro.

---

## 6. Pedido 1 — ver o catálogo (o mais importante)

Imagina: o browser já mostrou `index.html`. No fundo da página:

```html
<script src="/js/api.js"></script>
<script src="/js/app.js"></script>
```

Primeiro carrega `api.js` (a função `apiFetch`).
Depois `app.js` (o catálogo). A última linha de `app.js` é:

```js
loadVehicles();
```

Ou seja: assim que a página abre, pede os veículos à API.

### 6.1 Front-end monta o URL — `public/js/app.js`

```js
async function loadVehicles() {
    const params = new URLSearchParams();
    const q = document.querySelector('#q').value.trim();
    const tipo = document.querySelector('#tipo').value;
    // ... combustivel, estado ...

    if (q) params.set('q', q);
    if (tipo) params.set('tipo', tipo);

    const data = await apiFetch(`/api/veiculos?${params.toString()}`);
    renderVehicles(data.data);
}
```

Se escreveste `BMW`:

```text
GET /api/veiculos?q=BMW
```

`URLSearchParams` constrói a query string com segurança
(espaços viram `%20`, etc.).

`data.data` porque a API responde assim:

```json
{ "success": true, "total": 1, "data": [ /* veículos */ ] }
```

O array está dentro de `data`, não na raiz.

`renderVehicles` percorre esse array e injeta HTML dos cards.
`escapeHtml` troca `<` por `&lt;` para ninguém meter JavaScript
malicioso num nome de modelo (XSS).

### 6.2 `apiFetch` — o cliente HTTP — `public/js/api.js`

```js
async function apiFetch(url, options = {}) {
    const headers = { ...(options.headers || {}) };

    if (options.body && !headers['Content-Type']) {
        headers['Content-Type'] = 'application/json';
    }

    const token = getToken();
    if (token) {
        headers.Authorization = `Bearer ${token}`;
    }

    const response = await fetch(url, { ...options, headers });
    const data = await response.json().catch(() => ({}));

    if (!response.ok) {
        throw new Error(data.message || `Erro HTTP ${response.status}`);
    }
    return data;
}
```

Linha a linha:

1. Copia headers que o chamador tenha passado.
2. Se vai um `body`, marca `Content-Type: application/json`
   para o Express meter isso em `req.body`.
3. Se existir JWT no `localStorage`, envia
   `Authorization: Bearer ....`.
   No catálogo público **não há token** — e não faz falta,
   porque o GET é público.
4. `fetch` é o pedido HTTP (como o Postman, mas no browser).
5. `response.json()` transforma o texto JSON num objecto JavaScript.
6. Se o status for 400/401/404/500, lança erro.
   O `catch` em `loadVehicles` mostra o alerta vermelho.

Neste pedido o HTTP real é só:

```http
GET /api/veiculos?q=BMW HTTP/1.1
Host: localhost:3000
```

### 6.3 A rota escolhe o controller — `routes/veiculoRoutes.js`

```js
router.get('/', veiculoController.getAll);
router.get('/:id', veiculoController.getById);
```

Como `app.js` fez `app.use('/api/veiculos', veiculoRoutes)`:

| Pedido do browser              | Função                    | Precisa de login? |
|--------------------------------|---------------------------|-------------------|
| `GET /api/veiculos`            | `getAll`                  | Não               |
| `GET /api/veiculos/3`          | `getById`                 | Não               |
| `POST /api/veiculos`           | `create` + JWT + admin    | Sim               |
| `PUT /api/veiculos/3`          | `update` + JWT + admin    | Sim               |
| `DELETE /api/veiculos/3`       | `remove` + JWT + admin    | Sim               |

`/:id` é um parâmetro. Fica em `req.params.id`.
Em PHP seria algo como `$_GET['id']` ou um segmento do URL.

A ordem das rotas importa: `GET /` tem de estar **antes** de `GET /:id`.
Se fosse ao contrário, a palavra `veiculos` vazia podia ser lida como id.

### 6.4 O controller traduz HTTP em dados — `controllers/veiculoController.js`

```js
async function getAll(req, res, next) {
    try {
        const filters = {
            q: req.query.q || '',
            tipo: req.query.tipo || '',
            combustivel: req.query.combustivel || '',
            estado: req.query.estado || '',
            ano: req.query.ano || '',
            precoMin: req.query.precoMin || '',
            precoMax: req.query.precoMax || ''
        };

        const veiculos = await veiculoRepository.getAll(filters);

        res.json({
            success: true,
            total: veiculos.length,
            data: veiculos
        });
    } catch (error) {
        next(error);
    }
}
```

- `req.query.q` é o `BMW` da URL. Em PHP: `$_GET['q']`.
- O controller **não** monta o SQL. Pede ao repository.
- `res.json(...)` serializa para JSON. O Express, quando vê um
  objecto `Carro` ou `Moto`, chama automaticamente `toJSON()`.
- `next(error)` envia o erro para o `errorMiddleware`.
  Sem isto, um crash do MySQL podia deixar o pedido pendurado.

### 6.5 O repository fala com o MySQL — `repositories/veiculoRepository.js`

```js
async getAll(filters = {}) {
    const conditions = [];
    const params = [];

    if (filters.q) {
        conditions.push('(marca LIKE ? OR modelo LIKE ?)');
        params.push(`%${filters.q}%`, `%${filters.q}%`);
    }
    if (filters.tipo) {
        conditions.push('tipo = ?');
        params.push(filters.tipo);
    }
    // ... combustivel, estado, ano, precoMin, precoMax ...

    let sql = 'SELECT * FROM veiculos';
    if (conditions.length > 0) {
        sql += ` WHERE ${conditions.join(' AND ')}`;
    }
    sql += ' ORDER BY id DESC';

    const [rows] = await pool.execute(sql, params);
    return rows.map((row) => this.mapRow(row));
}
```

Para `q=BMW` o SQL fica:

```sql
SELECT * FROM veiculos
WHERE (marca LIKE ? OR modelo LIKE ?)
ORDER BY id DESC
```

Parâmetros: `'%BMW%'` e `'%BMW%'`.

O `?` é o sítio onde o mysql2 mete o valor com escape.
**Nunca** faças:

```js
sql += `WHERE marca LIKE '%${filters.q}%'`  // PERIGOSO
```

porque alguém podia escrever `' OR 1=1 --` na pesquisa.

`rows` ainda são objectos crus da BD (`{ id, tipo, marca, ... }`).
A linha seguinte transforma cada um numa classe:

```js
mapRow(row) {
    const dados = { id: row.id, marca: row.marca, /* ... */ };

    if (row.tipo === 'moto') {
        return new Moto(dados);
    }
    return new Carro(dados);
}
```

Aqui nasce o **polimorfismo**: a mesma função `mapRow` devolve
`Carro` ou `Moto` conforme a coluna `tipo`.

A classe `VeiculoRepository extends BaseRepository`.
O construtor faz `super('veiculos')` — a tabela chama-se `veiculos`.
`findById` e `delete` reutilizam `findRowById` / `deleteById` da classe base.

`BaseRepository` também se recusa a ser instanciada sozinha
(`new.target === BaseRepository`), como uma classe abstrata em PHP.

### 6.6 POO — `models/Veiculo.js`, `Carro.js`, `Moto.js`

JavaScript **não tem** `abstract class` nativa. O projecto simula:

```js
class Veiculo {
    constructor({ id = null, marca, modelo, ano, preco, ... }) {
        if (new.target === Veiculo) {
            throw new Error('Veiculo e uma classe abstrata...');
        }
        this.marca = marca;
        this.modelo = modelo;
        this.ano = Number(ano);
        this.preco = Number(preco);
        // ...
    }

    getTipo() {
        throw new Error('A classe filha tem de implementar getTipo().');
    }

    getInfoEspecifica() {
        throw new Error('A classe filha tem de implementar getInfoEspecifica().');
    }
}
```

- `new.target === Veiculo` → alguém fez `new Veiculo()`. Proibido.
  Só `new Carro(...)` ou `new Moto(...)` passam, porque aí
  `new.target` é `Carro` ou `Moto`.
- `getTipo()` e `getInfoEspecifica()` lançam erro na base.
  Obriga as filhas a implementar. Em PHP seria
  `abstract public function getTipo();`.

`Carro`:

```js
class Carro extends Veiculo {
    constructor(dados) {
        super(dados);                    // chama o construtor de Veiculo
        this.portas = Number(dados.portas);
    }
    getTipo() { return 'carro'; }
    getInfoEspecifica() { return `${this.portas} portas`; }
}
```

`Moto` faz o mesmo com `cilindradas` e devolve `"600 cc"`.

Quando o controller faz `res.json({ data: veiculos })`, o Express
chama `toJSON()` em cada objecto:

```js
toJSON() {
    return {
        id: this.id,
        tipo: this.getTipo(),                 // "carro" ou "moto"
        marca: this.marca,
        modelo: this.modelo,
        nomeCompleto: this.getNomeCompleto(), // "BMW M3"
        infoEspecifica: this.getInfoEspecifica(),
        // ...
    };
}
```

O front-end **nunca vê a classe**. Só vê JSON.
Mas os campos `tipo`, `nomeCompleto` e `infoEspecifica` nasceram
nos métodos das classes, não na tabela MySQL.
A tabela não tem coluna `nomeCompleto` — é calculada em `getNomeCompleto()`.

### 6.7 A tabela MySQL — `sql/database.sql`

```sql
CREATE TABLE veiculos (
    id INT AUTO_INCREMENT PRIMARY KEY,
    tipo ENUM('carro', 'moto') NOT NULL,
    marca VARCHAR(80) NOT NULL,
    modelo VARCHAR(100) NOT NULL,
    ano SMALLINT UNSIGNED NOT NULL,
    preco DECIMAL(10,2) NOT NULL,
    combustivel ENUM('gasolina', 'diesel', 'hibrido', 'eletrico', 'outro') NOT NULL,
    quilometragem INT UNSIGNED NOT NULL DEFAULT 0,
    portas TINYINT UNSIGNED NULL,       -- preenchido nos carros
    cilindradas INT UNSIGNED NULL,      -- preenchido nas motos
    estado ENUM('disponivel', 'reservado', 'vendido') NOT NULL DEFAULT 'disponivel',
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);
```

Uma tabela para os dois tipos. A coluna `tipo` decide a classe JS.
`portas` fica `NULL` numa moto; `cilindradas` fica `NULL` num carro.

Há um BMW M3 de exemplo. A pesquisa `q=BMW` encontra essa linha.

### 6.8 JSON de volta e o ecrã actualiza

Resposta típica:

```json
{
  "success": true,
  "total": 1,
  "data": [
    {
      "id": 1,
      "tipo": "carro",
      "marca": "BMW",
      "modelo": "M3",
      "nomeCompleto": "BMW M3",
      "ano": 2024,
      "preco": 89900,
      "combustivel": "gasolina",
      "quilometragem": 12000,
      "estado": "disponivel",
      "infoEspecifica": "4 portas",
      "portas": 4,
      "cilindradas": null
    }
  ]
}
```

O browser recebe isto, `apiFetch` devolve o objecto,
`renderVehicles` desenha o card.

Ao mesmo tempo, `requestLogger` espera pelo evento `finish` da resposta
e o `logger.js` acrescenta em `logs/app.log`:

```text
[2026-09-10T17:00:00.000Z] GET /api/veiculos?q=BMW -> 200 (12ms)
```

`fs.appendFile` é a matéria de **fs** do curso: gravar num ficheiro.
Se o log falhar, a app **não** rebenta — o `catch` só faz `console.error`.

### 6.9 Desenho do pedido 1 (decora isto)

```text
index.html carrega app.js
        │
        │  loadVehicles()
        ▼
apiFetch('/api/veiculos?q=BMW')          FRONT-END
        │
        │  HTTP GET + JSON
        ▼
app.js → veiculoRoutes GET /             EXPRESS
        │
        ▼
veiculoController.getAll                 CONTROLLER
        │  lê req.query, chama repository
        ▼
VeiculoRepository.getAll                 REPOSITORY
        │  SQL com ?
        ▼
MySQL tabela veiculos                    BASE DE DADOS
        │  linhas
        ▼
mapRow → new Carro / new Moto            POO
        │
        ▼
res.json(...) chama toJSON()             JSON
        │
        ▼
renderVehicles(data.data)                FRONT-END outra vez
```

---

## 7. Pedido 2 — login (JWT)

O admin abre `/login.html` e submete o formulário.

### 7.1 Front-end — `public/js/login.js`

```js
form.addEventListener('submit', async (event) => {
    event.preventDefault();   // não recarrega a página

    const data = await apiFetch('/api/auth/login', {
        method: 'POST',
        body: JSON.stringify({ email, password })
    });

    localStorage.setItem('autostand_token', data.token);
    localStorage.setItem('autostand_user', JSON.stringify(data.user));
    window.location.href = '/admin.html';
});
```

`JSON.stringify` transforma o objecto JS em texto JSON.
`localStorage` guarda o token no browser (fica lá mesmo se fechares o separador).
Isto é didático: numa app real muitas vezes usa-se cookie HttpOnly.

### 7.2 Rota pública — `routes/authRoutes.js`

```js
router.post('/login', authController.login);          // sem JWT
router.get('/me', authMiddleware, authController.me); // com JWT
```

Login tem de ser público: ainda não tens token.

### 7.3 Controller + serviço — `authController.js` e `authService.js`

O controller só verifica se vieram email e password e chama o serviço.
O SQL e o bcrypt **não** ficam no controller.

```js
// AuthService.login
const user = await userRepository.findByEmail(email.trim().toLowerCase());

if (!user) {
    throw new AppError('Email ou password incorretos.', 401);
}

const passwordOk = await bcrypt.compare(password, user.getPasswordHash());

if (!passwordOk) {
    throw new AppError('Email ou password incorretos.', 401);
}

return { token: this.gerarToken(user), user: user.toJSON() };
```

Pontos para o exame / para ti:

1. Email em minúsculas — `Admin@...` e `admin@...` são o mesmo.
2. A mensagem de erro é **igual** se o email não existe ou se a password
   está errada. Assim ninguém descobre quais emails estão registados.
3. `bcrypt.compare` compara a password em claro com o **hash** da coluna
   `users.password`. A password original nunca é guardada.
4. `user.toJSON()` **não inclui o hash**. A classe `User` guarda-o em
   `#passwordHash` (campo privado, o `#` é sintaxe JS).

`gerarToken`:

```js
return jwt.sign(
    { id: user.id, name: user.name, email: user.email, role: user.role },
    process.env.JWT_SECRET,
    { expiresIn: '1h' }
);
```

O JWT é um texto assinado. Qualquer pessoa o consegue *ler*, mas sem a
`JWT_SECRET` ninguém o consegue *forjar*. Por isso a secret não pode
ir para o Git.

O `seed.js` é quem cria o primeiro admin:

```js
const hash = await bcrypt.hash(password, 10);
await pool.execute(
    'INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?)',
    [name, email, hash, role]
);
```

`10` é o "custo" do bcrypt (quantas voltas). Quanto maior, mais lento
de adivinhar por força bruta.

### 7.4 Classe `User`

```js
class User {
    #passwordHash;   // privado: código de fora não faz user.passwordHash

    getPasswordHash() { return this.#passwordHash; }

    toJSON() {
        return { id, name, email, role, created_at }; // sem password
    }
}
```

Mesmo que um controller se engane e faça `res.json(user)`, o hash
não sai. É encapsulamento — ideia da POO.

---

## 8. Pedido 3 — criar um veículo (protegido)

Em `admin.html`, o JS começa por `checkLogin()`:

```js
if (!getToken()) {
    window.location.href = '/login.html';
}
const data = await apiFetch('/api/auth/me');
currentUser = data.user;
```

`GET /api/auth/me` já passa por `authMiddleware`.
Se o token for inválido ou tiver expirado, volta ao login.

### 8.1 Middleware de autenticação — `authMiddleware.js`

```js
const authorization = req.headers.authorization;

if (!authorization || !authorization.startsWith('Bearer ')) {
    throw new AppError('Token de autenticacao em falta.', 401);
}

const token = authorization.substring(7); // tira a palavra "Bearer "
const decoded = jwt.verify(token, process.env.JWT_SECRET);

req.user = decoded;  // { id, name, email, role, iat, exp }
next();              // passa ao próximo middleware / controller
```

`401` = "não sei quem tu és".
`next()` é o "está bom, continua".

### 8.2 Middleware de role — `roleMiddleware.js`

```js
function roleMiddleware(...allowedRoles) {
    return function checkRole(req, res, next) {
        if (!allowedRoles.includes(req.user.role)) {
            return next(new AppError('Nao tens permissao...', 403));
        }
        next();
    };
}
```

É uma **fábrica**: `roleMiddleware('admin')` devolve uma função.
`403` = "sei quem tu és, mas não podes fazer isto".
Diferença importante em APIs: 401 vs 403.

O viewer consegue `GET /api/vendas` (`roleMiddleware('admin', 'viewer')`)
mas não `POST /api/vendas` (só admin).

### 8.3 Validação — `validationMiddleware.js`

Antes de tocar na BD, confirma tipo, marca, ano, preço, portas/cc.
Se falhar, `400` e o repository nem corre. Menos SQL inútil, erros mais claros.

### 8.4 A cadeia completa do POST

```js
router.post(
    '/',
    authMiddleware,          // 1. JWT?
    roleMiddleware('admin'), // 2. admin?
    validateVehicle,         // 3. body válido?
    veiculoController.create // 4. gravar
);
```

O Express corre isto **em sequência**. Qualquer um pode parar com `next(error)`.

O controller:

```js
const veiculo = await veiculoRepository.insert(req.body);
res.status(201).json({ success: true, data: veiculo });
```

`201 Created` = "nasceu um recurso novo". `200` seria só "correu bem".

No `insert`, se `tipo === 'carro'` grava `portas` e `cilindradas = null`.
Se for moto, o contrário. Depois `findById` para devolver já um `Carro`/`Moto`.

O front-end, se já existir `vehicleId` no formulário, faz `PUT` (editar)
em vez de `POST` (criar). O mesmo formulário serve os dois casos.

---

## 9. Pedido 4 — registar uma venda (transacção)

Duas coisas têm de acontecer juntas:

1. `INSERT` na tabela `vendas`
2. `UPDATE veiculos SET estado = 'vendido'`

Se a segunda falhar e a primeira ficar, tens uma venda de um carro
que ainda aparece disponível. Por isso há **transacção**.

```js
const connection = await pool.getConnection();
await connection.beginTransaction();

const [vehicles] = await connection.execute(
    'SELECT * FROM veiculos WHERE id = ? FOR UPDATE',
    [vehicleId]
);

if (vehicle.estado === 'vendido') {
    throw new AppError('Este veiculo ja foi vendido.', 409);
}

await connection.execute('INSERT INTO vendas ...');
await connection.execute("UPDATE veiculos SET estado = 'vendido' WHERE id = ?");
await connection.commit();
```

- `FOR UPDATE` bloqueia aquela linha até ao `commit`.
  Dois admins não vendem o mesmo carro ao mesmo tempo.
- Se qualquer `throw` acontecer: `rollback()` no `catch`.
- `finally { connection.release() }` devolve a ligação ao pool
  **sempre**, mesmo quando há erro.

Em PHP: `$pdo->beginTransaction()`, `commit()`, `rollBack()`.

O `userId` da venda **não vem do formulário**. Vem de `req.user.id`
(o token). Assim ninguém finge que foi outro vendedor.

`409` = conflito: o carro já está vendido.

As chaves estrangeiras em `database.sql` impedem apagar um veículo
que já tenha vendas (`ON DELETE RESTRICT`). O `errorMiddleware`
apanha o código MySQL `ER_ROW_IS_REFERENCED_2` e devolve uma
mensagem legível em vez do erro cru.

---

## 10. Utilizadores — `userRoutes.js` e `userController.js`

```js
router.use(authMiddleware, roleMiddleware('admin'));
```

Aqui o middleware aplica-se a **todas** as rotas deste ficheiro.
Não precisas de o repetir em cada `get`/`post`/`delete`.

Criar user:

```js
const passwordHash = await bcrypt.hash(password, 10);
const user = await userRepository.insert({ name, email, passwordHash, role });
```

Outra vez: a password em claro nunca entra na BD.

Apagar:

```js
if (id === req.user.id) {
    throw new AppError('Nao podes apagar o utilizador com que tens sessao iniciada.', 400);
}
```

Protecção simples: o admin não se apaga a si próprio e fica a aplicação sem ninguém.

---

## 11. Erros — `utils/AppError.js` e `errorMiddleware.js`

```js
class AppError extends Error {
    constructor(message, statusCode = 500) {
        super(message);
        this.statusCode = statusCode;
    }
}
```

`throw new AppError('Veiculo nao encontrado.', 404)` carrega o código HTTP
dentro do erro. O middleware lê `error.statusCode` e faz `res.status(404).json(...)`.

Se for um erro inesperado (bug, MySQL em baixo), cai no `500`.

Isto substitui ter `try/catch` a devolver JSON em todos os controllers.
Os controllers só fazem `next(error)`.

---

## 12. Testes Jest — `tests/veiculo.test.js` e `tests/auth.test.js`

Não precisam da base de dados. Testam ideias de POO e JWT.

```js
test('Veiculo funciona como classe abstrata', () => {
    expect(() => new Veiculo({ ... })).toThrow('classe abstrata');
});

test('Polimorfismo: o mesmo metodo responde de forma diferente', () => {
    const veiculos = [new Carro({...}), new Moto({...})];
    const infos = veiculos.map((v) => v.getInfoEspecifica());
    expect(infos).toEqual(['5 portas', '600 cc']);
});
```

A mesma chamada `getInfoEspecifica()` — resultados diferentes.
Isso **é** polimorfismo. Se no exame te perguntarem, aponta para este teste.

`npm test` corre estes ficheiros.

---

## 13. Ordem para reler o código (com o editor aberto)

Não tentas ler tudo de uma vez. Esta ordem segue o pedido 1 e depois o 2:

1. `sql/database.sql` — as tabelas.
2. `config/database.js` — a ligação.
3. `models/Veiculo.js` → `Carro.js` → `Moto.js` — POO.
4. `repositories/BaseRepository.js` → `veiculoRepository.js` — SQL.
5. `controllers/veiculoController.js` — HTTP → repository.
6. `routes/veiculoRoutes.js` — URLs.
7. `app.js` + `server.js` — cola tudo.
8. `public/js/api.js` + `app.js` — o browser.
9. `services/authService.js` + `middleware/authMiddleware.js` — login.
10. `middleware/roleMiddleware.js` — admin vs viewer.
11. `repositories/vendaRepository.js` — transacção.
12. `utils/logger.js` + `tests/` — fs e Jest.

---

## 14. Perguntas para te testares (tenta sem olhar)

1. Porque é que `server.js` chama `testConnection()` **antes** de `app.listen`?
2. Quem monta o SQL do catálogo: o controller ou o repository? Porque?
3. O que acontece se fizeres `new Veiculo({...})`?
4. Onde nasce o campo JSON `nomeCompleto`, se a tabela não o tem?
5. Qual a diferença entre HTTP 401 e 403 neste projecto?
6. Porque é que o login devolve a mesma mensagem para email inexistente
   e para password errada?
7. Porque é que `User.toJSON()` não inclui a password?
8. O que faz `FOR UPDATE` na venda?
9. Porque é que o `userId` da venda vem de `req.user.id` e não do formulário?
10. Porque é que `GET /api/veiculos` não usa `authMiddleware`?

Respostas curtas (vê só depois):

1. Se o MySQL estiver em baixo, a app não arranca. Falha visível no terminal.
2. O repository. O controller não deve conhecer SQL.
3. Lança erro: classe abstrata simulada com `new.target`.
4. No método `getNomeCompleto()` da classe `Veiculo`, via `toJSON()`.
5. 401 = sem token / token inválido. 403 = token válido mas role errada.
6. Para não revelar quais emails existem na base de dados.
7. Encapsulamento: o hash está em `#passwordHash` e `toJSON` omite-o.
8. Bloqueia a linha do veículo até ao commit, para não haver duas vendas.
9. Para o vendedor ser quem está autenticado, não um id inventado no HTML.
10. O catálogo é público: qualquer visitante pode ver os veículos.

---

## 15. Mini-glossário

| Palavra        | Significado neste projecto                                      |
|----------------|-----------------------------------------------------------------|
| Rota           | URL + método HTTP (`GET /api/veiculos`)                         |
| Middleware     | Função que corre antes do controller (`auth`, `role`, `validate`)|
| Controller     | Recebe `req`/`res`, chama repository, responde JSON             |
| Repository     | Único sítio com SQL                                             |
| Pool           | Conjunto de ligações MySQL reutilizadas                         |
| JWT            | Token assinado que prova o login durante 1 hora                 |
| bcrypt         | Algoritmo que transforma password num hash                      |
| Polimorfismo   | `getInfoEspecifica()` faz coisas diferentes em Carro e Moto     |
| Transacção     | Vários SQL que ou passam todos ou nenhum                        |
| `next(error)`  | Entregar o erro ao `errorMiddleware`                            |

Se conseguires explicar o **pedido 1** em voz alta, do `fetch` até ao
`res.json`, já percebeste o esqueleto inteiro da aplicação. Os outros
pedidos só acrescentam JWT, roles e transacções em cima do mesmo caminho.
