import 'dotenv/config';
import cors from 'cors';
import express from 'express';
import { pool } from './db.js';

const app = express();
const port = process.env.PORT || 3000;

app.use(cors());
app.use(express.json());

app.get('/health', async (_req, res) => {
  try {
    await pool.query('SELECT 1');
    res.json({ status: 'ok', database: 'connected' });
  } catch (error) {
    res.status(500).json({ status: 'error', message: error.message });
  }
});

app.get('/api/fornecedores', async (_req, res, next) => {
  try {
    const [rows] = await pool.query(
      'SELECT id, nome, nif, email, telefone, especialidade FROM fornecedores ORDER BY nome'
    );
    res.json(rows);
  } catch (error) {
    next(error);
  }
});

app.get('/api/encomendas', async (_req, res, next) => {
  try {
    const [rows] = await pool.query(`
      SELECT e.id, e.referencia_peca, e.nome_peca, e.quantidade, e.observacoes,
             e.estado, e.criada_em, f.nome AS fornecedor
      FROM encomendas_fornecedores e
      INNER JOIN fornecedores f ON f.id = e.fornecedor_id
      ORDER BY e.criada_em DESC
    `);
    res.json(rows);
  } catch (error) {
    next(error);
  }
});

app.post('/api/encomendas', async (req, res, next) => {
  try {
    const { fornecedor_id, referencia_peca, nome_peca, quantidade, observacoes } = req.body;

    if (!fornecedor_id || !referencia_peca || !nome_peca || !quantidade) {
      return res.status(400).json({
        message: 'Campos obrigatorios: fornecedor_id, referencia_peca, nome_peca e quantidade.'
      });
    }

    const parsedQuantity = Number.parseInt(quantidade, 10);
    if (!Number.isInteger(parsedQuantity) || parsedQuantity <= 0) {
      return res.status(400).json({ message: 'A quantidade deve ser um numero inteiro positivo.' });
    }

    const [result] = await pool.execute(
      `INSERT INTO encomendas_fornecedores
       (fornecedor_id, referencia_peca, nome_peca, quantidade, observacoes)
       VALUES (:fornecedor_id, :referencia_peca, :nome_peca, :quantidade, :observacoes)`,
      {
        fornecedor_id,
        referencia_peca,
        nome_peca,
        quantidade: parsedQuantity,
        observacoes: observacoes || null
      }
    );

    const [rows] = await pool.execute(
      `SELECT e.id, e.referencia_peca, e.nome_peca, e.quantidade, e.observacoes,
              e.estado, e.criada_em, f.nome AS fornecedor
       FROM encomendas_fornecedores e
       INNER JOIN fornecedores f ON f.id = e.fornecedor_id
       WHERE e.id = :id`,
      { id: result.insertId }
    );

    res.status(201).json(rows[0]);
  } catch (error) {
    next(error);
  }
});

app.use((req, res) => {
  res.status(404).json({ message: `Rota nao encontrada: ${req.method} ${req.path}` });
});

app.use((error, _req, res, _next) => {
  console.error(error);
  res.status(500).json({ message: 'Erro interno na API de fornecedores.' });
});

app.listen(port, () => {
  console.log(`AutoLux fornecedores API running on port ${port}`);
});
