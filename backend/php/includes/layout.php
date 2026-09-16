<?php
function layout_inicio(string $titulo, string $paginaAtiva = ''): void
{
    $func = utilizador_atual();
    $flash = consumir_flash();
    ?>
<!DOCTYPE html>
<html lang="pt">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= e($titulo) ?> | AutoLux Backoffice</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/autolux.css">
</head>
<body>
<header class="navbar">
    <a class="brand" href="index.php">
        <span class="brand-mark">AL</span>
        <span>AutoLux <small>Backoffice</small></span>
    </a>
    <?php if ($func): ?>
    <nav class="nav-links" aria-label="Menu principal">
        <a class="<?= $paginaAtiva === 'inicio' ? 'ativo' : '' ?>" href="index.php">Início</a>
        <a class="<?= $paginaAtiva === 'pecas' ? 'ativo' : '' ?>" href="pecas.php">Catálogo</a>
        <a class="<?= $paginaAtiva === 'venda' ? 'ativo' : '' ?>" href="venda.php">Nova venda</a>
        <a class="<?= $paginaAtiva === 'vendas' ? 'ativo' : '' ?>" href="vendas.php">Vendas</a>
        <a class="<?= $paginaAtiva === 'clientes' ? 'ativo' : '' ?>" href="clientes.php">Clientes</a>
        <a class="<?= $paginaAtiva === 'fornecedores' ? 'ativo' : '' ?>" href="fornecedores.php">Fornecedores</a>
        <a class="<?= $paginaAtiva === 'encomendas' ? 'ativo' : '' ?>" href="encomendas.php">Encomendas</a>
    </nav>
    <div class="nav-user">
        <span><?= e($func['nome']) ?></span>
        <a class="btn btn-ghost" href="logout.php">Sair</a>
    </div>
    <?php endif; ?>
</header>
<main>
    <div class="container">
        <?php if ($flash): ?>
            <div class="flash flash-<?= e($flash['tipo']) ?>"><?= e($flash['mensagem']) ?></div>
        <?php endif; ?>
    <?php
}

function layout_fim(): void
{
    ?>
    </div>
</main>
<footer class="footer">
    <div class="container footer-row">
        <span>AutoLux — armazém de peças automóveis</span>
        <span>Fonte do catálogo: <?= e(fonte_dados_nome()) ?></span>
    </div>
</footer>
</body>
</html>
    <?php
}
