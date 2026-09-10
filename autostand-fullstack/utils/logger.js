// utils/logger.js
// Demonstra a materia de fs: gravar logs num ficheiro.

const fs = require('fs/promises');
const path = require('path');

const logDirectory = path.join(__dirname, '..', 'logs');
const logFile = path.join(logDirectory, 'app.log');

async function log(message) {
    try {
        await fs.mkdir(logDirectory, { recursive: true });
        const line = `[${new Date().toISOString()}] ${message}\n`;
        await fs.appendFile(logFile, line, 'utf8');
    } catch (error) {
        // Um erro de log nao deve derrubar a aplicacao.
        console.error('Erro ao escrever log:', error.message);
    }
}

module.exports = { log };
