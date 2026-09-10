// config/database.js
// Responsabilidade: criar a ligacao reutilizavel Node.js <-> MySQL.

require('dotenv').config();
const mysql = require('mysql2/promise');

const pool = mysql.createPool({
    host: process.env.DB_HOST || '127.0.0.1',
    port: Number(process.env.DB_PORT || 3306),
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    database: process.env.DB_NAME || 'autostand_db',
    waitForConnections: true,
    connectionLimit: 10,
    queueLimit: 0
});

async function testConnection() {
    const connection = await pool.getConnection();
    try {
        await connection.ping();
        console.log('Ligacao ao MySQL efetuada com sucesso.');
    } finally {
        connection.release();
    }
}

module.exports = {
    pool,
    testConnection
};
