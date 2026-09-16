<?php
/**
 * Carrega o ficheiro .env da pasta backend/ para variáveis de ambiente.
 * Não usa bibliotecas externas — o PHP do enunciado deve ser simples de correr.
 */
function carregarEnv(?string $ficheiro = null): void
{
    $ficheiro = $ficheiro ?: dirname(__DIR__, 2) . '/.env';
    if (!is_file($ficheiro)) {
        $exemplo = dirname(__DIR__, 2) . '/.env.example';
        if (is_file($exemplo)) {
            $ficheiro = $exemplo;
        } else {
            return;
        }
    }

    $linhas = file($ficheiro, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);
    if ($linhas === false) {
        return;
    }

    foreach ($linhas as $linha) {
        $linha = trim($linha);
        if ($linha === '' || str_starts_with($linha, '#')) {
            continue;
        }
        if (!str_contains($linha, '=')) {
            continue;
        }
        [$chave, $valor] = explode('=', $linha, 2);
        $chave = trim($chave);
        $valor = trim($valor);
        $valor = trim($valor, "\"'");
        if ($chave === '') {
            continue;
        }
        if (getenv($chave) === false) {
            putenv($chave . '=' . $valor);
            $_ENV[$chave] = $valor;
        }
    }
}

function env(string $chave, string $padrao = ''): string
{
    $valor = getenv($chave);
    if ($valor === false || $valor === '') {
        return $padrao;
    }
    return $valor;
}
