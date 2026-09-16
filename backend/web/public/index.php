<?php

declare(strict_types=1);

/**
 * Painel inicial da interface de gestão.
 * Junta indicadores da Base de Dados 1 (via PHP) com o resumo de compras
 * obtido no serviço Node.js, mostrando as duas camadas em funcionamento.
 */

require dirname(__DIR__) . '/bootstrap.php';

use App\Repository\ClienteRepository;
use App\Repository\PecaRepository;
use App\Repository\VendaRepository;
use App\Service\ComprasService;
use App\Support\Http\ApiException;
use App\Support\View;

$pecas = new PecaRepository();
$clientes = new ClienteRepository();
$vendas = new VendaRepository();
$compras = new ComprasService();

$resumoCompras = null;
$erroApi = null;

try {
    $resumoCompras = $compras->resumo();
} catch (ApiException $erro) {
    $erroApi = $erro->getMessage();
}

View::render('dashboard', [
    'titulo' => 'Painel de gestão',
    'paginaAtiva' => 'inicio',
    'totalPecas' => $pecas->totalPecas(),
    'valorStock' => $pecas->valorStock(),
    'totalClientes' => $clientes->total(),
    'totalVendas' => $vendas->totalVendas(),
    'vendasMes' => $vendas->totalVendasMes(),
    'ultimasVendas' => $vendas->ultimas(5),
    'pecasEmRutura' => $pecas->emRutura(),
    'resumoCompras' => $resumoCompras,
    'erroApi' => $erroApi,
]);
