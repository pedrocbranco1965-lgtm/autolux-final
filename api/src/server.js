import 'dotenv/config';
import { createApp } from './app.js';
import { createPool } from './db.js';

const port = Number(process.env.PORT ?? 3000);
const pool = createPool();
const app = createApp(pool);

const server = app.listen(port, () => {
  console.log(`API de fornecedores disponível em http://localhost:${port}/api`);
});

async function shutdown(signal) {
  console.log(`${signal} recebido. A encerrar...`);
  server.close(async () => {
    await pool.end();
    process.exit(0);
  });
}

process.on('SIGTERM', () => shutdown('SIGTERM'));
process.on('SIGINT', () => shutdown('SIGINT'));
