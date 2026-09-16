<?php

declare(strict_types=1);

/**
 * Arranque da interface de gestão: autoloading das classes App\, sessão e
 * tratamento uniforme de erros. Todas as páginas de public/ incluem este ficheiro.
 */

spl_autoload_register(static function (string $classe): void {
    $prefixo = 'App\\';
    if (!str_starts_with($classe, $prefixo)) {
        return;
    }

    $relativo = str_replace('\\', DIRECTORY_SEPARATOR, substr($classe, strlen($prefixo)));
    $ficheiro = __DIR__ . DIRECTORY_SEPARATOR . 'src' . DIRECTORY_SEPARATOR . $relativo . '.php';

    if (is_file($ficheiro)) {
        require $ficheiro;
    }
});

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (function_exists('mb_internal_encoding')) {
    mb_internal_encoding('UTF-8');
}

date_default_timezone_set('Europe/Lisbon');

/** Escapa texto antes de o imprimir em HTML. */
function e(mixed $valor): string
{
    return htmlspecialchars((string) $valor, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

/** Devolve o valor submetido anteriormente num formulário (para o repovoar). */
function old(array $dados, string $campo, mixed $omissao = ''): mixed
{
    return $dados[$campo] ?? $omissao;
}

/** Redireciona e termina o pedido (padrão Post/Redirect/Get). */
function redirecionar(string $destino): never
{
    header('Location: ' . $destino);
    exit;
}

set_exception_handler(static function (Throwable $erro): void {
    http_response_code(500);
    echo '<!doctype html><html lang="pt"><head><meta charset="utf-8">'
        . '<title>Erro - AutoLux</title>'
        . '<link rel="stylesheet" href="/assets/css/style.css"></head><body class="pagina-erro">'
        . '<div class="erro-caixa"><h1>Ocorreu um erro</h1><p>'
        . htmlspecialchars($erro->getMessage(), ENT_QUOTES)
        . '</p><p class="erro-dica">Verifique se o MySQL e o serviço Node.js estão a correr '
        . '(<code>npm run db:setup</code> e <code>npm start</code>).</p>'
        . '<a class="botao" href="/index.php">Voltar ao início</a></div></body></html>';
});
