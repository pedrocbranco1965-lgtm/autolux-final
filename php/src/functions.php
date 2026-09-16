<?php

function e(?string $value): string
{
    return htmlspecialchars($value ?? '', ENT_QUOTES, 'UTF-8');
}

function money(float|string $value): string
{
    return number_format((float) $value, 2, ',', ' ') . ' EUR';
}

function active_nav(string $page): string
{
    $current = basename($_SERVER['SCRIPT_NAME'], '.php');
    return $current === $page ? 'class="active"' : '';
}

function http_json(string $url, string $method = 'GET', ?array $payload = null): array
{
    $options = [
        'http' => [
            'method' => $method,
            'header' => "Content-Type: application/json\r\n",
            'ignore_errors' => true,
        ],
    ];

    if ($payload !== null) {
        $options['http']['content'] = json_encode($payload);
    }

    $response = file_get_contents($url, false, stream_context_create($options));
    if ($response === false) {
        return ['ok' => false, 'data' => null, 'error' => 'Nao foi possivel comunicar com a API Node.js.'];
    }

    $statusLine = $http_response_header[0] ?? 'HTTP/1.1 500';
    preg_match('/\s(\d{3})\s/', $statusLine, $matches);
    $status = isset($matches[1]) ? (int) $matches[1] : 500;
    $data = json_decode($response, true);

    return [
        'ok' => $status >= 200 && $status < 300,
        'data' => $data,
        'error' => $data['message'] ?? 'Erro na resposta da API Node.js.',
    ];
}
