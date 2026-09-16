const express = require('express');
const pool = require('../db');
const HttpError = require('../httpError');

const router = express.Router();

const ESTADOS = ['pendente', 'enviada', 'recebida', 'cancelada'];

/** Carrega uma encomenda com o nome do fornecedor e as respetivas linhas. */
async function obterEncomenda(id, ligacao = pool) {
  const [cabecalho] = await ligacao.query(
    `SELECT e.id, e.fornecedor_id, f.nome AS fornecedor_nome, e.data_encomenda,
            e.estado, e.total, e.observacoes, e.criado_por
       FROM encomendas e
       JOIN fornecedores f ON f.id = e.fornecedor_id
      WHERE e.id = ?`,
    [id]
  );
  if (cabecalho.length === 0) return null;

  const [itens] = await ligacao.query(
    `SELECT id, referencia_peca, descricao, quantidade, preco_unitario,
            quantidade * preco_unitario AS subtotal
       FROM encomendas_itens WHERE encomenda_id = ? ORDER BY id`,
    [id]
  );
  return { ...cabecalho[0], itens };
}

/**
 * GET /api/encomendas?estado=pendente&fornecedor_id=1
 */
router.get('/', async (req, res, next) => {
  try {
    const condicoes = [];
    const params = [];
    if (req.query.estado) {
      if (!ESTADOS.includes(req.query.estado)) throw new HttpError(400, `estado inválido (${ESTADOS.join(', ')})`);
      condicoes.push('e.estado = ?');
      params.push(req.query.estado);
    }
    if (req.query.fornecedor_id) {
      condicoes.push('e.fornecedor_id = ?');
      params.push(Number(req.query.fornecedor_id));
    }
    const where = condicoes.length ? `WHERE ${condicoes.join(' AND ')}` : '';

    const [linhas] = await pool.query(
      `SELECT e.id, e.fornecedor_id, f.nome AS fornecedor_nome, e.data_encomenda,
              e.estado, e.total, e.observacoes, e.criado_por,
              COUNT(i.id) AS num_linhas, COALESCE(SUM(i.quantidade), 0) AS total_unidades
         FROM encomendas e
         JOIN fornecedores f ON f.id = e.fornecedor_id
         LEFT JOIN encomendas_itens i ON i.encomenda_id = e.id
         ${where}
        GROUP BY e.id
        ORDER BY e.data_encomenda DESC, e.id DESC`,
      params
    );
    res.json(linhas);
  } catch (erro) {
    next(erro);
  }
});

/**
 * GET /api/encomendas/:id  (com itens)
 */
router.get('/:id', async (req, res, next) => {
  try {
    const encomenda = await obterEncomenda(req.params.id);
    if (!encomenda) throw new HttpError(404, 'Encomenda não encontrada');
    res.json(encomenda);
  } catch (erro) {
    next(erro);
  }
});

/**
 * POST /api/encomendas
 * Body JSON:
 * {
 *   "fornecedor_id": 1,
 *   "observacoes": "texto opcional",
 *   "criado_por": "nome do funcionário (opcional)",
 *   "itens": [
 *     { "referencia_peca": "BR-0986494", "descricao": "Pastilhas", "quantidade": 10, "preco_unitario": 28.6 }
 *   ]
 * }
 * Cabeçalho + linhas são gravados numa transação: ou fica tudo ou nada.
 */
router.post('/', async (req, res, next) => {
  const { fornecedor_id, observacoes, itens, criado_por } = req.body || {};
  const erros = [];

  if (!Number.isInteger(Number(fornecedor_id)) || Number(fornecedor_id) <= 0) erros.push('fornecedor_id inválido');
  if (!Array.isArray(itens) || itens.length === 0) erros.push('a encomenda precisa de pelo menos um item');
  else {
    itens.forEach((item, i) => {
      if (!item.referencia_peca) erros.push(`itens[${i}].referencia_peca é obrigatória`);
      if (!item.descricao) erros.push(`itens[${i}].descricao é obrigatória`);
      if (!Number.isInteger(Number(item.quantidade)) || Number(item.quantidade) <= 0) erros.push(`itens[${i}].quantidade deve ser inteiro > 0`);
      if (Number.isNaN(Number(item.preco_unitario)) || Number(item.preco_unitario) < 0) erros.push(`itens[${i}].preco_unitario inválido`);
    });
  }
  if (erros.length) return next(new HttpError(400, 'Dados inválidos', erros));

  const ligacao = await pool.getConnection();
  try {
    await ligacao.beginTransaction();

    const [fornecedor] = await ligacao.query('SELECT id, ativo FROM fornecedores WHERE id = ?', [fornecedor_id]);
    if (fornecedor.length === 0) throw new HttpError(404, 'Fornecedor não encontrado');
    if (!fornecedor[0].ativo) throw new HttpError(422, 'Fornecedor inativo');

    const total = itens.reduce((soma, it) => soma + Number(it.quantidade) * Number(it.preco_unitario), 0);

    const [cab] = await ligacao.query(
      'INSERT INTO encomendas (fornecedor_id, total, observacoes, criado_por) VALUES (?, ?, ?, ?)',
      [
        fornecedor_id,
        total.toFixed(2),
        observacoes ? String(observacoes).slice(0, 255) : null,
        criado_por ? String(criado_por).slice(0, 120) : null,
      ]
    );

    const valores = itens.map((it) => [
      cab.insertId,
      String(it.referencia_peca).trim(),
      String(it.descricao).trim().slice(0, 160),
      Number(it.quantidade),
      Number(it.preco_unitario).toFixed(2),
    ]);
    await ligacao.query(
      'INSERT INTO encomendas_itens (encomenda_id, referencia_peca, descricao, quantidade, preco_unitario) VALUES ?',
      [valores]
    );

    await ligacao.commit();
    const encomenda = await obterEncomenda(cab.insertId);
    res.status(201).json(encomenda);
  } catch (erro) {
    await ligacao.rollback();
    next(erro);
  } finally {
    ligacao.release();
  }
});

/**
 * PATCH /api/encomendas/:id/estado
 * Body JSON: { "estado": "enviada" }
 */
router.patch('/:id/estado', async (req, res, next) => {
  try {
    const { estado } = req.body || {};
    if (!ESTADOS.includes(estado)) throw new HttpError(400, `estado inválido (${ESTADOS.join(', ')})`);

    const [resultado] = await pool.query('UPDATE encomendas SET estado = ? WHERE id = ?', [estado, req.params.id]);
    if (resultado.affectedRows === 0) throw new HttpError(404, 'Encomenda não encontrada');

    res.json(await obterEncomenda(req.params.id));
  } catch (erro) {
    next(erro);
  }
});

module.exports = router;
