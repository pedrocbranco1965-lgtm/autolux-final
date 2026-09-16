import { pool, withTransaction } from '../db/pool.js';

const COLUNAS = `
    e.id,
    e.numero,
    e.fornecedor_id  AS fornecedorId,
    f.nome           AS fornecedorNome,
    e.estado,
    e.total,
    e.data_prevista  AS dataPrevista,
    e.observacoes,
    e.criada_em      AS criadaEm,
    e.atualizada_em  AS atualizadaEm
`;

export async function listar({ fornecedorId = null, estado = null } = {}) {
  const condicoes = [];
  const parametros = {};

  if (fornecedorId) {
    condicoes.push('e.fornecedor_id = :fornecedorId');
    parametros.fornecedorId = fornecedorId;
  }
  if (estado) {
    condicoes.push('e.estado = :estado');
    parametros.estado = estado;
  }

  const where = condicoes.length > 0 ? `WHERE ${condicoes.join(' AND ')}` : '';
  const [linhas] = await pool.execute(
    `SELECT ${COLUNAS},
            (SELECT COALESCE(SUM(i.quantidade), 0)
             FROM encomenda_itens i WHERE i.encomenda_id = e.id) AS totalArtigos
     FROM encomendas e
              INNER JOIN fornecedores f ON f.id = e.fornecedor_id
     ${where}
     ORDER BY e.criada_em DESC`,
    parametros,
  );
  return linhas;
}

export async function procurarPorId(id) {
  const [cabecalho] = await pool.execute(
    `SELECT ${COLUNAS}
     FROM encomendas e
              INNER JOIN fornecedores f ON f.id = e.fornecedor_id
     WHERE e.id = :id`,
    { id },
  );

  if (cabecalho.length === 0) {
    return null;
  }

  const [itens] = await pool.execute(
    `SELECT id, referencia, designacao, quantidade, preco_unitario AS precoUnitario, subtotal
     FROM encomenda_itens
     WHERE encomenda_id = :id
     ORDER BY id`,
    { id },
  );

  return { ...cabecalho[0], itens };
}

/**
 * Grava cabeçalho e linhas de uma encomenda numa única transação: se alguma
 * linha falhar, a encomenda não fica meio criada na base de dados.
 */
export async function criar({ fornecedorId, dataPrevista, observacoes, itens }) {
  return withTransaction(async (connection) => {
    const numero = await gerarNumero(connection);
    const total = itens.reduce((acumulado, item) => acumulado + item.subtotal, 0);

    const [resultado] = await connection.execute(
      `INSERT INTO encomendas (numero, fornecedor_id, estado, total, data_prevista, observacoes)
       VALUES (:numero, :fornecedorId, 'submetida', :total, :dataPrevista, :observacoes)`,
      {
        numero,
        fornecedorId,
        total: Math.round(total * 100) / 100,
        dataPrevista,
        observacoes,
      },
    );

    const encomendaId = resultado.insertId;

    for (const item of itens) {
      await connection.execute(
        `INSERT INTO encomenda_itens (encomenda_id, referencia, designacao, quantidade, preco_unitario, subtotal)
         VALUES (:encomendaId, :referencia, :designacao, :quantidade, :precoUnitario, :subtotal)`,
        { encomendaId, ...item },
      );
    }

    return encomendaId;
  });
}

export async function atualizarEstado(id, estado) {
  const [resultado] = await pool.execute(
    'UPDATE encomendas SET estado = :estado WHERE id = :id',
    { id, estado },
  );
  return resultado.affectedRows > 0;
}

export async function resumoPorEstado() {
  const [linhas] = await pool.execute(
    `SELECT estado, COUNT(*) AS total, COALESCE(SUM(total), 0) AS valor
     FROM encomendas
     GROUP BY estado`,
  );
  return linhas;
}

/**
 * Numeração sequencial por ano (E2026-0001, E2026-0002, ...). Corre dentro da
 * transação para que duas submissões simultâneas não gerem o mesmo número.
 */
async function gerarNumero(connection) {
  const ano = new Date().getFullYear();
  const [linhas] = await connection.execute(
    `SELECT COALESCE(MAX(CAST(SUBSTRING(numero, 7) AS UNSIGNED)), 0) AS ultimo
     FROM encomendas
     WHERE numero LIKE :prefixo
     FOR UPDATE`,
    { prefixo: `E${ano}-%` },
  );

  const proximo = Number(linhas[0].ultimo) + 1;
  return `E${ano}-${String(proximo).padStart(4, '0')}`;
}
