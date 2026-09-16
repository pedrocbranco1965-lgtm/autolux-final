/**
 * Pool de ligações à Base de Dados 2 (fornecedores e encomendas).
 * Um pool reutiliza ligações em vez de abrir uma nova por pedido.
 */
const mysql = require('mysql2/promise');

const pool = mysql.createPool({
  host: process.env.DB_HOST || '127.0.0.1',
  port: Number(process.env.DB_PORT || 3306),
  user: process.env.DB_USER || 'root',
  password: process.env.DB_PASSWORD || '',
  database: process.env.DB2_NAME || 'autolux_fornecedores',
  waitForConnections: true,
  connectionLimit: 10,
  // DECIMAL vem como string por defeito; convertemos para número para o JSON.
  decimalNumbers: true,
});

module.exports = pool;
