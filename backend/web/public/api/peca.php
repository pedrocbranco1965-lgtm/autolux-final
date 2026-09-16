<?php

declare(strict_types=1);

/**
 * Endpoint JSON interno: detalhe de uma peça da Base de Dados 1.
 * É consumido pelo formulário de encomenda para mostrar preço e stock atuais
 * sem recarregar a página.
 */

require dirname(__DIR__, 2) . '/bootstrap.php';

use App\Repository\PecaRepository;

header('Content-Type: application/json; charset=utf-8');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['erro' => 'Indique o identificador da peça.'], JSON_UNESCAPED_UNICODE);

    return;
}

$peca = (new PecaRepository())->procurarPorId($id);

if ($peca === null) {
    http_response_code(404);
    echo json_encode(['erro' => 'Peça não encontrada.'], JSON_UNESCAPED_UNICODE);

    return;
}

echo json_encode(['dados' => $peca], JSON_UNESCAPED_UNICODE);
