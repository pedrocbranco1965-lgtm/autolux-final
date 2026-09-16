<?php
/**
 * Lê o ficheiro .env da raiz do projeto (o mesmo usado pelo Node.js) e
 * devolve um array com a configuração. Se o .env não existir, usa os
 * valores por defeito, para o projeto arrancar "à primeira".
 */
declare(strict_types=1);

function carregarConfiguracao(): array
{
    $defaults = [
        'DB_HOST'      => '127.0.0.1',
        'DB_PORT'      => '3306',
        'DB_USER'      => 'root',
        'DB_PASSWORD'  => '',
        'DB1_NAME'     => 'autolux_vendas',
        'API_BASE_URL' => 'http://localhost:3000/api',
    ];

    $ficheiro = dirname(__DIR__, 2) . '/.env';
    if (!is_readable($ficheiro)) {
        return $defaults;
    }

    foreach (file($ficheiro, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) as $linha) {
        $linha = trim($linha);
        if ($linha === '' || str_starts_with($linha, '#') || !str_contains($linha, '=')) {
            continue;
        }
        [$chave, $valor] = explode('=', $linha, 2);
        $defaults[trim($chave)] = trim($valor, " \t\"'");
    }

    return $defaults;
}

return carregarConfiguracao();
