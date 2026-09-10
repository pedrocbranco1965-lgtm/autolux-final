// Cria utilizadores de teste com passwords cifradas por bcrypt.

require('dotenv').config();
const bcrypt = require('bcrypt');
const { pool, testConnection } = require('../config/database');

async function seedUser(name, email, password, role) {
    const [existing] = await pool.execute(
        'SELECT id FROM users WHERE email = ? LIMIT 1',
        [email]
    );

    if (existing.length > 0) {
        console.log(`Utilizador ${email} ja existe.`);
        return;
    }

    const hash = await bcrypt.hash(password, 10);

    await pool.execute(
        `INSERT INTO users (name, email, password, role)
         VALUES (?, ?, ?, ?)`,
        [name, email, hash, role]
    );

    console.log(`Criado: ${email} / ${password} (${role})`);
}

async function run() {
    try {
        await testConnection();
        await seedUser('Administrador AutoStand', 'admin@autostand.pt', 'admin123', 'admin');
        await seedUser('Utilizador Consulta', 'viewer@autostand.pt', 'viewer123', 'viewer');
    } catch (error) {
        console.error('Erro no seed:', error.message);
        process.exitCode = 1;
    } finally {
        await pool.end();
    }
}

run();
