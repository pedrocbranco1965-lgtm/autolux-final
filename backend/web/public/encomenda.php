<?php

declare(strict_types=1);

/** Detalhe de uma encomenda a fornecedor, obtido em GET /api/encomendas/:id. */

require dirname(__DIR__) . '/bootstrap.php';

use App\Service\ComprasService;
use App\Support\Flash;
use App\Support\Http\ApiException;
use App\Support\View;

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    Flash::erro('Encomenda inválida.');
    redirecionar('/encomendas.php');
}

try {
    $encomenda = (new ComprasService())->encomenda($id);
} catch (ApiException $erro) {
    Flash::erro($erro->getMessage());
    redirecionar('/encomendas.php');
}

View::render('encomenda-detalhe', [
    'titulo' => 'Encomenda ' . ($encomenda['numero'] ?? ''),
    'paginaAtiva' => 'encomendas',
    'encomenda' => $encomenda,
]);
