<?php

declare(strict_types=1);

/**
 * Requisito funcional C) Lista de fornecedores.
 * Os dados não vêm da base de dados local: são pedidos ao serviço Node.js, que
 * é quem acede à Base de Dados 2 (fornecedores e encomendas).
 */

require dirname(__DIR__) . '/bootstrap.php';

use App\Service\ComprasService;
use App\Support\Http\ApiException;
use App\Support\View;

$compras = new ComprasService();

$pesquisa = trim((string) ($_GET['q'] ?? ''));
$pais = trim((string) ($_GET['pais'] ?? ''));
$apenasAtivos = isset($_GET['ativos']);

$fornecedores = [];
$paises = [];
$erroApi = null;

try {
    $fornecedores = $compras->fornecedores($pesquisa ?: null, $pais ?: null, $apenasAtivos);
    $paises = $compras->paises();
} catch (ApiException $erro) {
    $erroApi = $erro->getMessage();
}

View::render('fornecedores', [
    'titulo' => 'Fornecedores',
    'paginaAtiva' => 'fornecedores',
    'fornecedores' => $fornecedores,
    'paises' => $paises,
    'pesquisa' => $pesquisa,
    'pais' => $pais,
    'apenasAtivos' => $apenasAtivos,
    'erroApi' => $erroApi,
]);
