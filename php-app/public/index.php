<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/functions.php';

$db = salesDb();
$stats = [
    'parts' => (int) $db->query('SELECT COUNT(*) FROM parts')->fetchColumn(),
    'stock' => (int) $db->query('SELECT COALESCE(SUM(stock), 0) FROM parts')->fetchColumn(),
    'clients' => (int) $db->query('SELECT COUNT(*) FROM clients')->fetchColumn(),
    'sales' => (float) $db->query('SELECT COALESCE(SUM(total), 0) FROM sales')->fetchColumn(),
];
$recentSales = $db->query(
    `SELECT s.id, c.name AS client, s.total, s.created_at
     FROM sales s JOIN clients c ON c.id = s.client_id
     ORDER BY s.created_at DESC LIMIT 5`
)->fetchAll();
$lowStock = $db->query(
    'SELECT name, sku, stock FROM parts WHERE stock <= 10 ORDER BY stock ASC LIMIT 5'
)->fetchAll();

$pageTitle = 'Resumo';
$activePage = 'dashboard';
require __DIR__ . '/../src/partials/header.php';
?>
<section class="page-heading">
    <div>
        <h1>Painel de gestão</h1>
        <p class="subtitle">Visão geral do armazém AutoLux.</p>
    </div>
    <a class="button gold" href="/sale.php">Registar venda</a>
</section>

<section class="stats">
    <article class="stat"><span>Referências</span><strong><?= $stats['parts'] ?></strong></article>
    <article class="stat"><span>Unidades em stock</span><strong><?= $stats['stock'] ?></strong></article>
    <article class="stat"><span>Clientes</span><strong><?= $stats['clients'] ?></strong></article>
    <article class="stat"><span>Total vendido</span><strong><?= money($stats['sales']) ?></strong></article>
</section>

<section class="two-columns">
    <article class="panel">
        <h2>Vendas recentes</h2>
        <?php if ($recentSales): ?>
            <div class="table-wrap">
                <table>
                    <thead><tr><th>N.º</th><th>Cliente</th><th>Total</th></tr></thead>
                    <tbody>
                    <?php foreach ($recentSales as $sale): ?>
                        <tr>
                            <td>#<?= (int) $sale['id'] ?></td>
                            <td><?= h($sale['client']) ?></td>
                            <td><?= money($sale['total']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="empty">Ainda não existem vendas.</p>
        <?php endif; ?>
    </article>

    <article class="panel">
        <h2>Stock baixo</h2>
        <div class="table-wrap">
            <table>
                <thead><tr><th>Peça</th><th>SKU</th><th>Stock</th></tr></thead>
                <tbody>
                <?php foreach ($lowStock as $part): ?>
                    <tr>
                        <td><?= h($part['name']) ?></td>
                        <td><?= h($part['sku']) ?></td>
                        <td class="stock-low"><?= (int) $part['stock'] ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </article>
</section>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
