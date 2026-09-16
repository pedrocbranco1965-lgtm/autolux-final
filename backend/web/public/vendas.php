<?php

declare(strict_types=1);

/** Histórico de vendas registadas na Base de Dados 1, com filtro por cliente. */

require dirname(__DIR__) . '/bootstrap.php';

use App\Repository\ClienteRepository;
use App\Repository\VendaRepository;
use App\Support\View;

$vendas = new VendaRepository();
$clientes = new ClienteRepository();

$clienteId = isset($_GET['cliente_id']) && $_GET['cliente_id'] !== '' ? (int) $_GET['cliente_id'] : null;
$lista = $vendas->listar($clienteId);

View::render('vendas', [
    'titulo' => 'Vendas',
    'paginaAtiva' => 'vendas',
    'vendas' => $lista,
    'clientes' => $clientes->listar(),
    'clienteId' => $clienteId,
    'totalFaturado' => array_sum(array_map(
        static fn (array $venda): float => $venda['estado'] === 'anulada' ? 0.0 : (float) $venda['total'],
        $lista,
    )),
]);
