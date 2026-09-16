<?php
declare(strict_types=1);

require_once __DIR__ . '/config.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}

function h(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function money(float|string $value): string
{
    return number_format((float) $value, 2, ',', '.') . ' €';
}

function csrfToken(): string
{
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}

function verifyCsrf(): void
{
    $token = $_POST['csrf_token'] ?? '';
    if (!is_string($token) || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
        http_response_code(419);
        exit('Pedido expirado. Volte à página anterior e tente novamente.');
    }
}

function flash(string $type, string $message): void
{
    $_SESSION['flash'] = ['type' => $type, 'message' => $message];
}

function consumeFlash(): ?array
{
    $flash = $_SESSION['flash'] ?? null;
    unset($_SESSION['flash']);
    return $flash;
}

function redirect(string $path): never
{
    header("Location: {$path}");
    exit;
}

function apiRequest(string $path, string $method = 'GET', ?array $payload = null): array
{
    $headers = ['Accept: application/json'];
    $options = [
        'method' => $method,
        'timeout' => 5,
        'ignore_errors' => true,
    ];

    if ($payload !== null) {
        $headers[] = 'Content-Type: application/json';
        $options['content'] = json_encode($payload, JSON_THROW_ON_ERROR);
    }
    $options['header'] = implode("\r\n", $headers);

    $context = stream_context_create(['http' => $options]);
    $body = @file_get_contents(suppliersApiUrl() . $path, false, $context);
    if ($body === false) {
        throw new RuntimeException('Não foi possível contactar a API de fornecedores.');
    }

    $statusLine = $http_response_header[0] ?? '';
    preg_match('/\s(\d{3})\s/', $statusLine, $matches);
    $status = (int) ($matches[1] ?? 500);
    $data = json_decode($body, true, 512, JSON_THROW_ON_ERROR);

    return ['status' => $status, 'data' => $data];
}
