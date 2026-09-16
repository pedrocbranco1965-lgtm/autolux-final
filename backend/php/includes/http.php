<?php
function http_get_json(string $url): array
{
    $resultado = http_pedido('GET', $url);
    if ($resultado['codigo'] < 200 || $resultado['codigo'] >= 300) {
        throw new RuntimeException('Pedido GET falhou (' . $resultado['codigo'] . '): ' . $resultado['corpo']);
    }
    $dados = json_decode($resultado['corpo'], true);
    if (!is_array($dados)) {
        throw new RuntimeException('A resposta não é JSON válido.');
    }
    return $dados;
}

function http_post_json(string $url, array $corpo): array
{
    $resultado = http_pedido('POST', $url, $corpo);
    $dados = json_decode($resultado['corpo'], true);
    if ($resultado['codigo'] < 200 || $resultado['codigo'] >= 300) {
        $mensagem = is_array($dados) && isset($dados['erro']) ? $dados['erro'] : $resultado['corpo'];
        throw new RuntimeException($mensagem);
    }
    if (!is_array($dados)) {
        throw new RuntimeException('A resposta não é JSON válido.');
    }
    return $dados;
}

function http_pedido(string $metodo, string $url, ?array $corpo = null): array
{
    $conteudo = $corpo !== null ? json_encode($corpo, JSON_UNESCAPED_UNICODE) : null;
    if (function_exists('curl_init')) {
        $ch = curl_init($url);
        curl_setopt_array($ch, [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST => $metodo,
            CURLOPT_TIMEOUT => 8,
            CURLOPT_HTTPHEADER => ['Accept: application/json', 'Content-Type: application/json'],
        ]);
        if ($conteudo !== null) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $conteudo);
        }
        $resposta = curl_exec($ch);
        $codigo = (int) curl_getinfo($ch, CURLINFO_HTTP_CODE);
        $erro = curl_error($ch);
        curl_close($ch);
        if ($resposta === false) {
            throw new RuntimeException('Falha de rede: ' . $erro);
        }
        return ['codigo' => $codigo, 'corpo' => $resposta];
    }

    $opcoes = [
        'http' => [
            'method' => $metodo,
            'header' => "Accept: application/json\r\nContent-Type: application/json\r\n",
            'timeout' => 8,
            'ignore_errors' => true,
        ],
    ];
    if ($conteudo !== null) {
        $opcoes['http']['content'] = $conteudo;
    }
    $contexto = stream_context_create($opcoes);
    $resposta = @file_get_contents($url, false, $contexto);
    $codigo = 0;
    if (isset($http_response_header[0]) && preg_match('/\s(\d{3})\s/', $http_response_header[0], $m)) {
        $codigo = (int) $m[1];
    }
    if ($resposta === false) {
        throw new RuntimeException('Falha de rede ao contactar ' . $url);
    }
    return ['codigo' => $codigo, 'corpo' => $resposta];
}
