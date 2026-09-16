import mysql from 'mysql2/promise';

export const pool = mysql.createPool({
  host: process.env.DB_HOST || 'localhost',
  user: process.env.DB_USER || 'autolux',
  password: process.env.DB_PASS || 'autolux123',
  database: process.env.DB_NAME || 'autolux_fornecedores',
  waitForConnections: true,
  connectionLimit: 10,
  namedPlaceholders: true
});
