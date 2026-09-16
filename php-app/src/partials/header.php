<?php
$pageTitle = $pageTitle ?? 'Gestão';
$activePage = $activePage ?? '';
$flashMessage = consumeFlash();
?>
<!doctype html>
<html lang="pt">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= h($pageTitle) ?> | AutoLux</title>
    <link rel="stylesheet" href="/assets/style.css">
</head>
<body>
<header class="topbar">
    <a class="brand" href="/">
        <span class="brand-mark">A</span>
        <span>AutoLux <small>Gestão de peças</small></span>
    </a>
    <nav aria-label="Navegação principal">
        <a class="<?= $activePage === 'dashboard' ? 'active' : '' ?>" href="/">Resumo</a>
        <a class="<?= $activePage === 'catalog' ? 'active' : '' ?>" href="/catalog.php">Catálogo</a>
        <a class="<?= $activePage === 'sale' ? 'active' : '' ?>" href="/sale.php">Nova venda</a>
        <a class="<?= $activePage === 'clients' ? 'active' : '' ?>" href="/clients.php">Clientes</a>
        <a class="<?= $activePage === 'sales' ? 'active' : '' ?>" href="/sales.php">Vendas</a>
        <a class="<?= $activePage === 'suppliers' ? 'active' : '' ?>" href="/suppliers.php">Fornecedores</a>
    </nav>
</header>
<main class="container">
    <?php if ($flashMessage): ?>
        <div class="alert <?= h($flashMessage['type']) ?>" role="alert">
            <?= h($flashMessage['message']) ?>
        </div>
    <?php endif; ?>
