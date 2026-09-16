<?php
function e(?string $texto): string
{
    return htmlspecialchars((string) $texto, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function dinheiro(float|int|string|null $valor): string
{
    return number_format((float) $valor, 2, ',', ' ') . ' €';
}

function redirecionar(string $url): void
{
    header('Location: ' . $url);
    exit;
}

function csrf_token(): string
{
    if (empty($_SESSION['csrf'])) {
        $_SESSION['csrf'] = bin2hex(random_bytes(16));
    }
    return $_SESSION['csrf'];
}

function csrf_ok(): bool
{
    $enviado = $_POST['csrf'] ?? '';
    return is_string($enviado) && hash_equals(csrf_token(), $enviado);
}

function flash(string $tipo, string $mensagem): void
{
    $_SESSION['flash'] = ['tipo' => $tipo, 'mensagem' => $mensagem];
}

function consumir_flash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return is_array($flash) ? $flash : null;
}

function metodo_post(): bool
{
    return ($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST';
}

function fonte_dados_nome(): string
{
    return match (FONTE_DADOS) {
        'A' => 'A — Ficheiro JSON',
        'B' => 'B — API REST',
        default => 'C — Base de dados MySQL',
    };
}
