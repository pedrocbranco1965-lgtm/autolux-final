'use strict';

const express = require('express');

function criarRotasEncomendas(pool) {
  const router = express.Router();

  router.get('/', async (_req, res) => {
    try {
      const [linhas] = await pool.query(`
        SELECT e.id, e.fornecedor_id, f.nome AS fornecedor_nome,
               e.peca_referencia, e.peca_nome, e.quantidade, e.preco_previsto,
               e.observacoes, e.estado, e.criado_em
        FROM encomendas e
        INNER JOIN fornecedores f ON f.id = e.fornecedor_id
        ORDER BY e.criado_em DESC
      `);
      res.json(linhas);
    } catch (erro) {
      console.error(erro);
      res.status(500).json({ erro: 'Não foi possível listar as encomendas.' });
    }
  });

  router.get('/:id', async (req, res) => {
    try {
      const [linhas] = await pool.query(
        `SELECT e.id, e.fornecedor_id, f.nome AS fornecedor_nome,
                e.peca_referencia, e.peca_nome, e.quantidade, e.preco_previsto,
                e.observacoes, e.estado, e.criado_em
         FROM encomendas e
         INNER JOIN fornecedores f ON f.id = e.fornecedor_id
         WHERE e.id = :id`,
        { id: req.params.id }
      );
      if (!linhas.length) {
        res.status(404).json({ erro: 'Encomenda não encontrada.' });
        return;
      }
      res.json(linhas[0]);
    } catch (erro) {
      console.error(erro);
      res.status(500).json({ erro: 'Erro ao obter a encomenda.' });
    }
  });

  router.post('/', async (req, res) => {
    const {
      fornecedor_id,
      peca_referencia,
      peca_nome,
      quantidade,
      preco_previsto,
      observacoes,
    } = req.body || {};

    const erros = [];
    if (!fornecedor_id) erros.push('fornecedor_id é obrigatório.');
    if (!peca_referencia || String(peca_referencia).trim() === '') erros.push('peca_referencia é obrigatória.');
    if (!peca_nome || String(peca_nome).trim() === '') erros.push('peca_nome é obrigatório.');
    if (!Number.isInteger(Number(quantidade)) || Number(quantidade) < 1) {
      erros.push('quantidade deve ser um inteiro maior que 0.');
    }
    if (Number.isNaN(Number(preco_previsto)) || Number(preco_previsto) < 0) {
      erros.push('preco_previsto deve ser um número igual ou maior que 0.');
    }

    if (erros.length) {
      res.status(400).json({ erro: 'Dados inválidos.', detalhes: erros });
      return;
    }

    try {
      const [fornecedor] = await pool.query(
        'SELECT id FROM fornecedores WHERE id = :id',
        { id: fornecedor_id }
      );
      if (!fornecedor.length) {
        res.status(400).json({ erro: 'Fornecedor inexistente.' });
        return;
      }

      const [resultado] = await pool.query(
        `INSERT INTO encomendas
          (fornecedor_id, peca_referencia, peca_nome, quantidade, preco_previsto, observacoes, estado)
         VALUES
          (:fornecedor_id, :peca_referencia, :peca_nome, :quantidade, :preco_previsto, :observacoes, 'pendente')`,
        {
          fornecedor_id,
          peca_referencia: String(peca_referencia).trim(),
          peca_nome: String(peca_nome).trim(),
          quantidade: Number(quantidade),
          preco_previsto: Number(preco_previsto),
          observacoes: observacoes ? String(observacoes).trim() : null,
        }
      );

      const [criada] = await pool.query(
        `SELECT e.id, e.fornecedor_id, f.nome AS fornecedor_nome,
                e.peca_referencia, e.peca_nome, e.quantidade, e.preco_previsto,
                e.observacoes, e.estado, e.criado_em
         FROM encomendas e
         INNER JOIN fornecedores f ON f.id = e.fornecedor_id
         WHERE e.id = :id`,
        { id: resultado.insertId }
      );

      res.status(201).json(criada[0]);
    } catch (erro) {
      console.error(erro);
      res.status(500).json({ erro: 'Não foi possível gravar a encomenda.' });
    }
  });

  return router;
}

module.exports = { criarRotasEncomendas };
