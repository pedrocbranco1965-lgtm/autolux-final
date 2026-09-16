<?php
/**
 * Router para o servidor embutido do PHP (php -S ... router.php).
 * Serve ficheiros estáticos (CSS/JS) diretamente e envia o resto para
 * o .php correspondente. Em Apache/XAMPP este ficheiro não é necessário.
 */
$caminho = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if ($caminho === '/' || $caminho === '') {
    require __DIR__ . '/index.php';
    return true;
}

if (is_file(__DIR__ . $caminho)) {
    return false; // deixa o servidor embutido servir o ficheiro (php ou estático)
}

http_response_code(404);
require __DIR__ . '/../src/bootstrap.php';
$titulo = 'Página não encontrada';
require __DIR__ . '/../templates/cabecalho.php';
echo '<section class="cartao vazio"><h1>Página não encontrada</h1><p>O endereço <code>' . e($caminho) . '</code> não existe.</p><a class="botao" href="index.php">Voltar ao painel</a></section>';
require __DIR__ . '/../templates/rodape.php';
return true;
