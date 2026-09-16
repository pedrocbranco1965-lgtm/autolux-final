import { pool } from '../db/pool.js';

const COLUNAS = `
    f.id,
    f.nome,
    f.nif,
    f.email,
    f.telefone,
    f.pais,
    f.prazo_entrega_dias AS prazoEntregaDias,
    f.ativo,
    f.criado_em          AS criadoEm
`;

/**
 * Lista fornecedores aplicando apenas os filtros recebidos. As condições são
 * acumuladas com parâmetros nomeados para evitar concatenação de valores no SQL.
 */
export async function listar({ pesquisa = null, pais = null, apenasAtivos = false } = {}) {
  const condicoes = [];
  const parametros = {};

  if (pesquisa) {
    condicoes.push('(f.nome LIKE :pesquisa OR f.email LIKE :pesquisa OR f.nif LIKE :pesquisa)');
    parametros.pesquisa = `%${pesquisa}%`;
  }
  if (pais) {
    condicoes.push('f.pais = :pais');
    parametros.pais = pais;
  }
  if (apenasAtivos) {
    condicoes.push('f.ativo = 1');
  }

  const where = condicoes.length > 0 ? `WHERE ${condicoes.join(' AND ')}` : '';
  const [linhas] = await pool.execute(
    `SELECT ${COLUNAS},
            (SELECT COUNT(*) FROM encomendas e WHERE e.fornecedor_id = f.id) AS totalEncomendas
     FROM fornecedores f
     ${where}
     ORDER BY f.ativo DESC, f.nome`,
    parametros,
  );

  return linhas;
}

export async function procurarPorId(id) {
  const [linhas] = await pool.execute(
    `SELECT ${COLUNAS} FROM fornecedores f WHERE f.id = :id`,
    { id },
  );
  return linhas[0] ?? null;
}

export async function listarArtigos(fornecedorId) {
  const [linhas] = await pool.execute(
    `SELECT id, referencia, designacao, preco_custo AS precoCusto
     FROM fornecedor_artigos
     WHERE fornecedor_id = :fornecedorId
     ORDER BY designacao`,
    { fornecedorId },
  );
  return linhas;
}

export async function listarPaises() {
  const [linhas] = await pool.execute(
    'SELECT DISTINCT pais FROM fornecedores ORDER BY pais',
  );
  return linhas.map((linha) => linha.pais);
}
