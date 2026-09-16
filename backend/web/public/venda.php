<?php

declare(strict_types=1);

/** Detalhe de uma venda: cabeçalho, linhas e dados do pagamento. */

require dirname(__DIR__) . '/bootstrap.php';

use App\Repository\VendaRepository;
use App\Support\Flash;
use App\Support\View;

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$venda = $id > 0 ? (new VendaRepository())->procurarPorId($id) : null;

if ($venda === null) {
    Flash::erro('A venda pedida não existe.');
    redirecionar('/vendas.php');
}

View::render('venda-detalhe', [
    'titulo' => 'Venda ' . $venda['numero'],
    'paginaAtiva' => 'vendas',
    'venda' => $venda,
]);
