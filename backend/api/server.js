import { createApp } from './app.js';
import { config } from './config/env.js';
import { closePool, pool } from './db/pool.js';

const app = createApp();

async function iniciar() {
  try {
    const ligacao = await pool.getConnection();
    ligacao.release();
    console.log(`[api] ligado à base de dados "${config.database.compras}"`);
  } catch (error) {
    console.error(`[api] não foi possível ligar ao MySQL: ${error.message}`);
    console.error('[api] confirme as credenciais no ficheiro .env e corra "npm run db:setup".');
    process.exitCode = 1;
    return;
  }

  const servidor = app.listen(config.api.port, () => {
    console.log(`[api] serviço de compras a correr em http://127.0.0.1:${config.api.port}`);
  });

  const encerrar = async (sinal) => {
    console.log(`\n[api] ${sinal} recebido, a encerrar...`);
    servidor.close(async () => {
      await closePool();
      process.exit(0);
    });
  };

  process.on('SIGINT', () => encerrar('SIGINT'));
  process.on('SIGTERM', () => encerrar('SIGTERM'));
}

iniciar();
