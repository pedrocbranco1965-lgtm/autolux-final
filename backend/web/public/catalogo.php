<?php

declare(strict_types=1);

/**
 * Requisito funcional A) Catálogo de peças.
 * Lista as peças da Base de Dados 1 com filtros por marca, tipo de peça e
 * gama de preço (mais pesquisa por texto e ordenação).
 */

require dirname(__DIR__) . '/bootstrap.php';

use App\Service\CatalogoService;
use App\Support\View;

$catalogo = new CatalogoService();

$filtros = $catalogo->normalizarFiltros($_GET);
$pecas = $catalogo->pesquisar($filtros);
$opcoes = $catalogo->opcoesDeFiltro();

View::render('catalogo', [
    'titulo' => 'Catálogo de peças',
    'paginaAtiva' => 'catalogo',
    'pecas' => $pecas,
    'filtros' => $filtros,
    'marcas' => $opcoes['marcas'],
    'tipos' => $opcoes['tipos'],
    'precos' => $opcoes['precos'],
    'temFiltros' => $catalogo->temFiltrosAtivos($filtros),
]);
