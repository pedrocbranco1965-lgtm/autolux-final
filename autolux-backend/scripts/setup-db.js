/**
 * Cria as duas bases de dados (BD1 vendas e BD2 fornecedores) e carrega
 * os dados de exemplo, executando os ficheiros .sql da pasta database/.
 *
 * Uso:  npm run db:setup
 *
 * Alternativa manual (cliente mysql):
 *   mysql -u root -p < database/bd1_vendas.sql
 *   mysql -u root -p < database/bd2_fornecedores.sql
 */
require('dotenv').config({ path: require('path').join(__dirname, '..', '.env') });

const fs = require('fs');
const path = require('path');
const mysql = require('mysql2/promise');

const ficheiros = ['bd1_vendas.sql', 'bd2_fornecedores.sql'];

async function main() {
  const ligacao = await mysql.createConnection({
    host: process.env.DB_HOST || '127.0.0.1',
    port: Number(process.env.DB_PORT || 3306),
    user: process.env.DB_USER || 'root',
    password: process.env.DB_PASSWORD || '',
    multipleStatements: true,
  });

  try {
    for (const nome of ficheiros) {
      const caminho = path.join(__dirname, '..', 'database', nome);
      const sql = fs.readFileSync(caminho, 'utf8');
      process.stdout.write(`A executar ${nome}... `);
      await ligacao.query(sql);
      console.log('OK');
    }
    console.log('\nBases de dados criadas com sucesso:');
    console.log(`  - ${process.env.DB1_NAME || 'autolux_vendas'} (clientes, peças, vendas)`);
    console.log(`  - ${process.env.DB2_NAME || 'autolux_fornecedores'} (fornecedores, encomendas)`);
  } finally {
    await ligacao.end();
  }
}

main().catch((erro) => {
  console.error('\nERRO ao criar as bases de dados:', erro.message);
  console.error('Verifique as credenciais no ficheiro .env (DB_HOST, DB_USER, DB_PASSWORD).');
  process.exit(1);
});
