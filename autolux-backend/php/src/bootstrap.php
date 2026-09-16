<?php
/**
 * Ficheiro incluído no topo de todas as páginas em php/public/.
 * Trata de: configuração, autoload das classes, sessão e funções auxiliares.
 */
declare(strict_types=1);

mb_internal_encoding('UTF-8');
date_default_timezone_set('Europe/Lisbon');
error_reporting(E_ALL);
ini_set('display_errors', '1');

// Configuração (.env) disponível globalmente
$config = require dirname(__DIR__) . '/config/env.php';

// Autoload PSR-4 simples: AutoLux\Repositorios\PecaRepository -> src/Repositorios/PecaRepository.php
spl_autoload_register(function (string $classe): void {
    $prefixo = 'AutoLux\\';
    if (!str_starts_with($classe, $prefixo)) {
        return;
    }
    $relativo = str_replace('\\', '/', substr($classe, strlen($prefixo)));
    $ficheiro = __DIR__ . '/' . $relativo . '.php';
    if (is_file($ficheiro)) {
        require $ficheiro;
    }
});

// Registo dos métodos de pagamento (ver config/pagamentos.php)
$pagamentos = new AutoLux\Pagamentos\RegistoPagamentos(require dirname(__DIR__) . '/config/pagamentos.php');

// Cliente da API Node.js
$apiFornecedores = new AutoLux\Api\FornecedoresApiClient($config['API_BASE_URL']);

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---------------------------------------------------------------------------
// Funções auxiliares usadas nas páginas/templates
// ---------------------------------------------------------------------------

/** Escapa texto para HTML (evita XSS). Usar SEMPRE ao imprimir dados. */
function e(mixed $valor): string
{
    return htmlspecialchars((string) ($valor ?? ''), ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}

function formatarPreco(float|string|null $valor): string
{
    return number_format((float) $valor, 2, ',', '.') . ' €';
}

function formatarData(?string $data, string $formato = 'd/m/Y H:i'): string
{
    if (!$data) {
        return '-';
    }
    return (new DateTimeImmutable($data))->format($formato);
}

/** Guarda uma mensagem para mostrar na próxima página (padrão "flash"). */
function flash(string $tipo, string $mensagem): void
{
    $_SESSION['flash'][] = ['tipo' => $tipo, 'mensagem' => $mensagem];
}

/** Devolve e limpa as mensagens flash. */
function obterFlash(): array
{
    $mensagens = $_SESSION['flash'] ?? [];
    unset($_SESSION['flash']);
    return $mensagens;
}

function redirecionar(string $url): never
{
    header('Location: ' . $url);
    exit;
}

/** Valor antigo de um campo de formulário (para repor após erro de validação). */
function antigo(string $campo, mixed $defeito = ''): string
{
    return e($_POST[$campo] ?? $defeito);
}

/** Etiqueta (badge) de estado de encomenda com classe CSS. */
function badgeEstado(string $estado): string
{
    $classes = ['pendente' => 'aviso', 'enviada' => 'info', 'recebida' => 'sucesso', 'cancelada' => 'erro'];
    return '<span class="badge badge-' . ($classes[$estado] ?? 'neutro') . '">' . e($estado) . '</span>';
}
