import mysql from 'mysql2/promise';
import { config } from '../config/env.js';

/**
 * Pool de ligações à Base de Dados 2 (fornecedores e encomendas).
 * Só este módulo conhece as credenciais: os repositórios recebem a pool já
 * configurada, o que mantém o acesso a dados isolado do resto do serviço.
 */
export const pool = mysql.createPool({
  host: config.database.host,
  port: config.database.port,
  user: config.database.user,
  password: config.database.password,
  database: config.database.compras,
  waitForConnections: true,
  connectionLimit: 10,
  namedPlaceholders: true,
  decimalNumbers: true,
});

export async function query(sql, params = {}) {
  const [rows] = await pool.execute(sql, params);
  return rows;
}

/**
 * Executa um conjunto de operações dentro de uma transação, garantindo
 * commit/rollback e devolução da ligação à pool em qualquer cenário.
 */
export async function withTransaction(callback) {
  const connection = await pool.getConnection();
  try {
    await connection.beginTransaction();
    const result = await callback(connection);
    await connection.commit();
    return result;
  } catch (error) {
    await connection.rollback();
    throw error;
  } finally {
    connection.release();
  }
}

export async function closePool() {
  await pool.end();
}
