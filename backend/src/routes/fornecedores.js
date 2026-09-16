'use strict';

const express = require('express');

function criarRotasFornecedores(pool) {
  const router = express.Router();

  router.get('/', async (_req, res) => {
    try {
      const [linhas] = await pool.query(
        'SELECT id, nome, email, telefone, nif, morada, especialidade, prazo_entrega_dias FROM fornecedores ORDER BY nome'
      );
      res.json(linhas);
    } catch (erro) {
      console.error(erro);
      res.status(500).json({ erro: 'Não foi possível listar os fornecedores.' });
    }
  });

  router.get('/:id', async (req, res) => {
    try {
      const [linhas] = await pool.query(
        'SELECT id, nome, email, telefone, nif, morada, especialidade, prazo_entrega_dias FROM fornecedores WHERE id = :id',
        { id: req.params.id }
      );
      if (!linhas.length) {
        res.status(404).json({ erro: 'Fornecedor não encontrado.' });
        return;
      }
      res.json(linhas[0]);
    } catch (erro) {
      console.error(erro);
      res.status(500).json({ erro: 'Erro ao obter o fornecedor.' });
    }
  });

  return router;
}

module.exports = { criarRotasFornecedores };
