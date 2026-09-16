import { spawn } from 'node:child_process';
import path from 'node:path';
import { config } from '../api/config/env.js';

/**
 * Arranca a interface de gestão PHP com o servidor embutido do PHP, para que o
 * projeto corra sem depender de Apache configurado à mão.
 */
const raizPublica = path.join(config.projectRoot, 'web', 'public');
const comandoPhp = process.env.PHP_BIN ?? 'php';

const processo = spawn(
  comandoPhp,
  ['-S', `127.0.0.1:${config.web.port}`, '-t', raizPublica],
  { stdio: 'inherit' },
);

processo.on('error', (erro) => {
  if (erro.code === 'ENOENT') {
    console.error('[web] PHP não encontrado no PATH.');
    console.error('[web] instale o PHP (ou o XAMPP) e, se necessário, indique o caminho em PHP_BIN.');
  } else {
    console.error(`[web] erro ao arrancar o PHP: ${erro.message}`);
  }
  process.exit(1);
});

processo.on('exit', (codigo) => process.exit(codigo ?? 0));
