import { spawn } from 'node:child_process';
import path from 'node:path';
import { config } from '../api/config/env.js';

/**
 * Arranca os dois processos da solução (serviço Node.js de compras e interface
 * de gestão PHP) com um único "npm start". Se um deles morrer, o outro é
 * terminado para não deixar portas ocupadas.
 */
const processos = [];
let aEncerrar = false;

function arrancar(nome, comando, argumentos) {
  const processo = spawn(comando, argumentos, {
    cwd: config.projectRoot,
    stdio: 'inherit',
    shell: process.platform === 'win32',
  });

  processo.on('exit', (codigo) => {
    if (!aEncerrar) {
      console.error(`\n[start] o processo "${nome}" terminou com o código ${codigo}.`);
      encerrar(codigo ?? 1);
    }
  });

  processos.push({ nome, processo });
  return processo;
}

function encerrar(codigo = 0) {
  if (aEncerrar) {
    return;
  }
  aEncerrar = true;
  for (const { processo } of processos) {
    processo.kill('SIGTERM');
  }
  setTimeout(() => process.exit(codigo), 300);
}

console.log('AutoLux - a arrancar a solução completa\n');
arrancar('api', process.execPath, [path.join('api', 'server.js')]);
arrancar('web', process.execPath, [path.join('scripts', 'start-web.js')]);

setTimeout(() => {
  console.log('\n----------------------------------------------------------');
  console.log(`  Interface de gestão (PHP) : http://127.0.0.1:${config.web.port}`);
  console.log(`  API de compras (Node.js)  : http://127.0.0.1:${config.api.port}/api`);
  console.log('  Terminar com Ctrl+C');
  console.log('----------------------------------------------------------\n');
}, 1200);

process.on('SIGINT', () => encerrar(0));
process.on('SIGTERM', () => encerrar(0));
