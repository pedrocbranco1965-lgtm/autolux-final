const express = require('express');
const pool = require('../db');
const HttpError = require('../httpError');

const router = express.Router();

/**
 * GET /api/fornecedores
 * Lista os fornecedores. Por defeito só os ativos; ?todos=1 inclui inativos.
 */
router.get('/', async (req, res, next) => {
  try {
    const incluirInativos = req.query.todos === '1';
    const sql = `
      SELECT f.id, f.nome, f.nif, f.email, f.telefone, f.morada,
             f.prazo_entrega_dias, f.ativo, f.criado_em,
             COUNT(e.id) AS total_encomendas
        FROM fornecedores f
        LEFT JOIN encomendas e ON e.fornecedor_id = f.id
       ${incluirInativos ? '' : 'WHERE f.ativo = 1'}
       GROUP BY f.id
       ORDER BY f.nome`;
    const [linhas] = await pool.query(sql);
    res.json(linhas);
  } catch (erro) {
    next(erro);
  }
});

/**
 * GET /api/fornecedores/:id
 */
router.get('/:id', async (req, res, next) => {
  try {
    const [linhas] = await pool.query('SELECT * FROM fornecedores WHERE id = ?', [req.params.id]);
    if (linhas.length === 0) throw new HttpError(404, 'Fornecedor não encontrado');
    res.json(linhas[0]);
  } catch (erro) {
    next(erro);
  }
});

/**
 * POST /api/fornecedores
 * Body JSON: { nome, nif, email, telefone?, morada?, prazo_entrega_dias? }
 */
router.post('/', async (req, res, next) => {
  try {
    const { nome, nif, email, telefone, morada, prazo_entrega_dias } = req.body || {};
    const erros = [];
    if (!nome || String(nome).trim().length < 2) erros.push('nome é obrigatório');
    if (!/^\d{9}$/.test(String(nif || ''))) erros.push('nif deve ter 9 dígitos');
    if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email)) erros.push('email inválido');
    if (erros.length) throw new HttpError(400, 'Dados inválidos', erros);

    const [resultado] = await pool.query(
      `INSERT INTO fornecedores (nome, nif, email, telefone, morada, prazo_entrega_dias)
       VALUES (?, ?, ?, ?, ?, ?)`,
      [nome.trim(), nif, email.trim(), telefone || null, morada || null, Number(prazo_entrega_dias) || 5]
    );
    const [linhas] = await pool.query('SELECT * FROM fornecedores WHERE id = ?', [resultado.insertId]);
    res.status(201).json(linhas[0]);
  } catch (erro) {
    if (erro.code === 'ER_DUP_ENTRY') return next(new HttpError(409, 'Já existe um fornecedor com esse NIF'));
    next(erro);
  }
});

module.exports = router;
