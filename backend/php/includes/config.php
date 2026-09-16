<?php
/**
 * Constantes da aplicação. A fonte de dados do catálogo troca-se aqui
 * (ou no .env) sem alterar as páginas PHP.
 *
 *   A = JSON   backend/data/pecas.json
 *   B = API    php/api_pecas.php  (JSON via HTTP)
 *   C = MySQL  Base de Dados 1     ← versão entregue
 */
define('FONTE_DADOS', strtoupper(env('FONTE_DADOS', 'C')));
define('DB1_HOST', env('DB1_HOST', '127.0.0.1'));
define('DB1_PORT', env('DB1_PORT', '3306'));
define('DB1_NAME', env('DB1_NAME', 'autolux_vendas'));
define('DB1_USER', env('DB1_USER', 'autolux'));
define('DB1_PASS', env('DB1_PASS', 'autolux'));
define('API_FORNECEDORES_URL', rtrim(env('API_FORNECEDORES_URL', 'http://127.0.0.1:3000'), '/'));
define('APP_URL', rtrim(env('APP_URL', 'http://127.0.0.1:8080'), '/'));
define('JSON_PECAS', dirname(__DIR__, 2) . '/data/pecas.json');
