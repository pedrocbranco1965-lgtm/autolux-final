<?php

/**
 * @var string $titulo
 * @var string $paginaAtiva
 */

use App\Support\Flash;

$menu = [
    'inicio' => ['etiqueta' => 'Início', 'url' => '/index.php'],
    'catalogo' => ['etiqueta' => 'Catálogo de peças', 'url' => '/catalogo.php'],
    'nova-venda' => ['etiqueta' => 'Nova encomenda', 'url' => '/nova-venda.php'],
    'vendas' => ['etiqueta' => 'Vendas', 'url' => '/vendas.php'],
    'clientes' => ['etiqueta' => 'Clientes', 'url' => '/clientes.php'],
    'fornecedores' => ['etiqueta' => 'Fornecedores', 'url' => '/fornecedores.php'],
    'encomendas' => ['etiqueta' => 'Encomendas a fornecedores', 'url' => '/encomendas.php'],
];
?>
<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?> | AutoLux</title>
    <link rel="stylesheet" href="/assets/css/style.css">
</head>
<body>
<header class="topo">
    <div class="topo__marca">
        <a href="/index.php">
            <span class="topo__logo">AutoLux</span>
            <span class="topo__sub">Armazém de peças automóveis</span>
        </a>
    </div>
    <nav class="topo__nav">
        <?php foreach ($menu as $chave => $item) : ?>
            <a class="topo__link<?= $paginaAtiva === $chave ? ' topo__link--ativo' : '' ?>"
               href="<?= e($item['url']) ?>"><?= e($item['etiqueta']) ?></a>
        <?php endforeach; ?>
    </nav>
</header>

<main class="conteudo">
    <?php foreach (Flash::consumir() as $mensagem) : ?>
        <div class="alerta alerta--<?= e($mensagem['tipo']) ?>"><?= e($mensagem['mensagem']) ?></div>
    <?php endforeach; ?>
