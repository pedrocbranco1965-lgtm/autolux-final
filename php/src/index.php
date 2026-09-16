<?php
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/db.php';

$stats = [
    'pecas' => db()->query('SELECT COUNT(*) AS total FROM pecas')->fetch()['total'],
    'clientes' => db()->query('SELECT COUNT(*) AS total FROM clientes')->fetch()['total'],
    'vendas' => db()->query('SELECT COUNT(*) AS total FROM vendas')->fetch()['total'],
];

render_header('Inicio');
?>

<section class="hero">
    <div>
        <p class="eyebrow">Gestao interna</p>
        <h1>AutoLux Backend</h1>
        <p>
            Solucao web para funcionarios consultarem stock, registarem vendas e criarem
            encomendas a fornecedores atraves de uma API Node.js.
        </p>
        <a class="button" href="/catalogo.php">Ver catalogo de pecas</a>
    </div>
</section>

<section class="grid stats">
    <article>
        <strong><?= e((string) $stats['pecas']) ?></strong>
        <span>Pecas em catalogo</span>
    </article>
    <article>
        <strong><?= e((string) $stats['clientes']) ?></strong>
        <span>Clientes registados</span>
    </article>
    <article>
        <strong><?= e((string) $stats['vendas']) ?></strong>
        <span>Vendas efetuadas</span>
    </article>
</section>

<section class="card">
    <h2>Componentes do projeto</h2>
    <ul class="checklist">
        <li>PHP com paginas dinamicas para catalogo, clientes e vendas.</li>
        <li>MySQL para guardar clientes, pecas, stock e vendas.</li>
        <li>API Node.js para fornecedores e encomendas a fornecedores.</li>
        <li>Segunda base MySQL dedicada aos fornecedores.</li>
    </ul>
</section>

<?php render_footer(); ?>
