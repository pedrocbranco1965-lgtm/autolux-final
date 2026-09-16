'use strict';

/**
 * API REST de fornecedores e encomendas (camada Node.js do enunciado).
 *
 * Endpoints:
 *   GET  /api/health
 *   GET  /api/fornecedores
 *   GET  /api/fornecedores/:id
 *   GET  /api/encomendas
 *   GET  /api/encomendas/:id
 *   POST /api/encomendas
 */
const path = require('path');
const express = require('express');
const cors = require('cors');
const dotenv = require('dotenv');

dotenv.config({ path: path.join(__dirname, '..', '.env') });

const { criarPool, esperarMysql } = require('./db');
const { criarRotasFornecedores } = require('./routes/fornecedores');
const { criarRotasEncomendas } = require('./routes/encomendas');

const app = express();
const porto = Number(process.env.API_PORT || 3000);

app.use(cors());
app.use(express.json());

app.get('/api/health', (_req, res) => {
  res.json({ ok: true, servico: 'autolux-fornecedores-api' });
});

async function arrancar() {
  const pool = criarPool();
  await esperarMysql(pool);

  app.use('/api/fornecedores', criarRotasFornecedores(pool));
  app.use('/api/encomendas', criarRotasEncomendas(pool));

  app.use((_req, res) => {
    res.status(404).json({ erro: 'Rota não encontrada.' });
  });

  app.listen(porto, '0.0.0.0', () => {
    console.log(`[api] AutoLux fornecedores a escutar em http://127.0.0.1:${porto}`);
  });
}

arrancar().catch((erro) => {
  console.error('[api] Falha a arrancar:', erro);
  process.exit(1);
});
