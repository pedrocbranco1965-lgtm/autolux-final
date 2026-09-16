import mysql from 'mysql2/promise';

export function createPool() {
  return mysql.createPool({
    host: process.env.DB_HOST ?? 'localhost',
    port: Number(process.env.DB_PORT ?? 3308),
    database: process.env.DB_NAME ?? 'autolux_fornecedores',
    user: process.env.DB_USER ?? 'autolux',
    password: process.env.DB_PASSWORD ?? 'autolux',
    waitForConnections: true,
    connectionLimit: 10,
    decimalNumbers: true,
  });
}
