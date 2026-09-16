<?php
require_once __DIR__ . '/functions.php';

function render_header(string $title): void
{
    ?>
    <!doctype html>
    <html lang="pt">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?= e($title) ?> | AutoLux</title>
        <link rel="stylesheet" href="/styles.css">
    </head>
    <body>
        <header class="topbar">
            <div>
                <strong class="brand">AutoLux</strong>
                <span>Armazem de pecas automoveis</span>
            </div>
            <nav>
                <a <?= active_nav('index') ?> href="/index.php">Inicio</a>
                <a <?= active_nav('catalogo') ?> href="/catalogo.php">Catalogo</a>
                <a <?= active_nav('venda') ?> href="/venda.php">Venda a cliente</a>
                <a <?= active_nav('fornecedores') ?> href="/fornecedores.php">Fornecedores</a>
            </nav>
        </header>
        <main class="container">
    <?php
}

function render_footer(): void
{
    ?>
        </main>
        <footer class="footer">
            Projeto final AutoLux - PHP, Node.js e MySQL
        </footer>
    </body>
    </html>
    <?php
}
