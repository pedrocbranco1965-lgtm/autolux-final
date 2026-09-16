import fs from 'node:fs/promises';
import path from 'node:path';
import mysql from 'mysql2/promise';
import { config } from '../api/config/env.js';

const FICHEIROS = ['01_bd1_vendas.sql', '02_bd2_compras.sql'];

/**
 * Cria as duas bases de dados e carrega os dados de exemplo.
 * Evita depender do cliente "mysql" da linha de comandos, que nem sempre está
 * no PATH em instalações XAMPP/WAMP.
 */
async function main() {
  const { host, port, user, password } = config.database;
  console.log(`[db] a ligar a ${user}@${host}:${port} ...`);

  const ligacao = await mysql.createConnection({
    host,
    port,
    user,
    password,
    multipleStatements: true,
  });

  try {
    for (const ficheiro of FICHEIROS) {
      const caminho = path.join(config.projectRoot, 'sql', ficheiro);
      const sql = await fs.readFile(caminho, 'utf8');
      console.log(`[db] a executar ${ficheiro} ...`);
      await ligacao.query(sql);
    }

    const [[vendas]] = await ligacao.query(
      `SELECT (SELECT COUNT(*) FROM \`${config.database.vendas}\`.pecas)    AS pecas,
              (SELECT COUNT(*) FROM \`${config.database.vendas}\`.clientes) AS clientes`,
    );
    const [[compras]] = await ligacao.query(
      `SELECT (SELECT COUNT(*) FROM \`${config.database.compras}\`.fornecedores) AS fornecedores,
              (SELECT COUNT(*) FROM \`${config.database.compras}\`.encomendas)   AS encomendas`,
    );

    console.log('\n[db] bases de dados criadas com sucesso:');
    console.log(`     ${config.database.vendas}  -> ${vendas.pecas} peças, ${vendas.clientes} clientes`);
    console.log(`     ${config.database.compras} -> ${compras.fornecedores} fornecedores, ${compras.encomendas} encomendas`);
    console.log('\n[db] já pode correr "npm start".');
  } finally {
    await ligacao.end();
  }
}

main().catch((erro) => {
  console.error(`\n[db] falhou: ${erro.message}`);
  if (erro.code === 'ECONNREFUSED') {
    console.error('[db] o servidor MySQL parece estar desligado (no XAMPP: iniciar o módulo MySQL).');
  }
  if (erro.code === 'ER_ACCESS_DENIED_ERROR') {
    console.error('[db] utilizador ou palavra-passe inválidos: reveja DB_USER/DB_PASSWORD no ficheiro .env.');
  }
  process.exit(1);
});
