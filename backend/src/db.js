'use strict';

/**
 * Ligação à Base de Dados 2 (fornecedores / encomendas).
 * Só o Node.js usa esta base — o PHP nunca se liga aqui.
 */
const mysql = require('mysql2/promise');

function criarPool() {
  return mysql.createPool({
    host: process.env.DB2_HOST || '127.0.0.1',
    port: Number(process.env.DB2_PORT || 3306),
    user: process.env.DB2_USER || 'autolux',
    password: process.env.DB2_PASS || 'autolux',
    database: process.env.DB2_NAME || 'autolux_fornecedores',
    waitForConnections: true,
    connectionLimit: 10,
    namedPlaceholders: true,
    charset: 'utf8mb4',
  });
}

async function esperarMysql(pool, tentativas = 20) {
  for (let i = 1; i <= tentativas; i += 1) {
    try {
      const ligacao = await pool.getConnection();
      ligacao.release();
      return;
    } catch (erro) {
      console.log(`[api] MySQL ainda não está pronto (${i}/${tentativas}): ${erro.message}`);
      await new Promise((resolve) => setTimeout(resolve, 1500));
    }
  }
  throw new Error('Não foi possível ligar à Base de Dados 2 (MySQL).');
}

module.exports = { criarPool, esperarMysql };
