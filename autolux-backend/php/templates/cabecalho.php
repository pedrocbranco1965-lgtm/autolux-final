<?php
/**
 * Cabeçalho comum a todas as páginas. Espera a variável $titulo definida
 * pela página que o inclui. $paginaAtual é usada para marcar o menu ativo.
 */
$paginaAtual = basename($_SERVER['SCRIPT_NAME'], '.php');
$menu = [
    'index'                  => ['Painel',            'index.php'],
    'pecas'                  => ['Catálogo de peças', 'pecas.php'],
    'clientes'               => ['Clientes',          'clientes.php'],
    'venda'                  => ['Nova venda',        'venda.php'],
    'vendas'                 => ['Vendas',            'vendas.php'],
    'fornecedores'           => ['Fornecedores',      'fornecedores.php'],
    'encomendas_fornecedor'  => ['Encomendas',        'encomendas_fornecedor.php'],
];
$ativos = [
    'venda_detalhe'        => 'vendas',
    'encomenda_fornecedor' => 'encomendas_fornecedor',
];
$paginaAtiva = $ativos[$paginaAtual] ?? $paginaAtual;
?>
<!doctype html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($titulo ?? 'AutoLux') ?> | AutoLux Gestão</title>
  <link rel="stylesheet" href="assets/estilos.css">
</head>
<body>
<header class="topo">
  <a class="marca" href="index.php"><span class="marca-simbolo">AL</span> AutoLux <small>Armazém de peças</small></a>
  <nav class="menu" aria-label="Menu principal">
    <?php foreach ($menu as $chave => [$rotulo, $url]): ?>
      <a href="<?= $url ?>" class="<?= $paginaAtiva === $chave ? 'ativo' : '' ?>"><?= $rotulo ?></a>
    <?php endforeach; ?>
  </nav>
</header>

<main class="conteudo">
  <?php foreach (obterFlash() as $f): ?>
    <div class="alerta alerta-<?= e($f['tipo']) ?>"><?= e($f['mensagem']) ?></div>
  <?php endforeach; ?>
