// server.js
// Responsabilidade: arrancar o servidor HTTP.

require('dotenv').config();

const { getJwtSecret } = require('./utils/authCookie');
const app = require('./app');
const { testConnection } = require('./config/database');

const PORT = Number(process.env.PORT || 3000);

async function startServer() {
    try {
        getJwtSecret();
        await testConnection();

        app.listen(PORT, () => {
            console.log(`AutoStand a correr em http://localhost:${PORT}`);
        });
    } catch (error) {
        console.error('Nao foi possivel arrancar o servidor:', error.message);
        process.exit(1);
    }
}

startServer();
