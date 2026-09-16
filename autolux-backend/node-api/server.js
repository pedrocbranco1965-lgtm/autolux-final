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
