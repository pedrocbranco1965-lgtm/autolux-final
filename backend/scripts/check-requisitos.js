import { execFile } from 'node:child_process';
import { promisify } from 'node:util';
import mysql from 'mysql2/promise';
import { config } from '../api/config/env.js';

const executar = promisify(execFile);

const OK = '  [OK]   ';
const FALHA = '  [ERRO] ';

async function verificarPhp() {
  const comando = process.env.PHP_BIN ?? 'php';

  try {
    const { stdout } = await executar(comando, ['-r', 'echo PHP_VERSION, "|", implode(",", get_loaded_extensions());']);
    const [versao, extensoes] = stdout.split('|');
    console.log(`${OK}PHP ${versao} encontrado.`);

    const necessarias = ['pdo_mysql', 'json'];
    const recomendadas = ['curl', 'mbstring'];
    const instaladas = extensoes.split(',').map((nome) => nome.toLowerCase());

    for (const extensao of necessarias) {
      const presente = instaladas.includes(extensao);
      console.log(`${presente ? OK : FALHA}extensão ${extensao} ${presente ? 'ativa' : 'EM FALTA (obrigatória)'}`);
    }
    for (const extensao of recomendadas) {
      const presente = instaladas.includes(extensao);
      console.log(`${presente ? OK : '  [AVISO]'} extensão ${extensao} ${presente ? 'ativa' : 'em falta (recomendada)'}`);
    }
  } catch {
    console.log(`${FALHA}PHP não encontrado no PATH (instale o XAMPP ou defina PHP_BIN).`);
  }
}

async function verificarBaseDados() {
  const { host, port, user, password, vendas, compras } = config.database;

  try {
    const ligacao = await mysql.createConnection({ host, port, user, password });
    console.log(`${OK}MySQL acessível em ${host}:${port} com o utilizador "${user}".`);

    const [bases] = await ligacao.query('SHOW DATABASES');
    const nomes = bases.map((linha) => Object.values(linha)[0]);

    for (const base of [vendas, compras]) {
      const existe = nomes.includes(base);
      console.log(`${existe ? OK : FALHA}base de dados "${base}" ${existe ? 'criada' : 'EM FALTA (correr npm run db:setup)'}`);
    }

    await ligacao.end();
  } catch (erro) {
    console.log(`${FALHA}sem ligação ao MySQL: ${erro.message}`);
  }
}

console.log('\nAutoLux - verificação de requisitos\n');
console.log(`  Node.js ${process.version}`);
await verificarPhp();
await verificarBaseDados();
console.log(`\n  Portas configuradas: PHP ${config.web.port} · API Node.js ${config.api.port}\n`);
