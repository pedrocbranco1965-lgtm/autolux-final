<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/functions.php';

$sales = salesDb()->query(
    `SELECT s.id, s.total, s.created_at, c.name AS client,
            pm.name AS payment, p.name AS part, si.quantity, si.unit_price
     FROM sales s
     JOIN clients c ON c.id = s.client_id
     JOIN payment_methods pm ON pm.id = s.payment_method_id
     JOIN sale_items si ON si.sale_id = s.id
     JOIN parts p ON p.id = si.part_id
     ORDER BY s.created_at DESC`
)->fetchAll();

$pageTitle = 'Vendas';
$activePage = 'sales';
require __DIR__ . '/../src/partials/header.php';
?>
<section class="page-heading">
    <div>
        <h1>Vendas</h1>
        <p class="subtitle">Histórico das peças vendidas a clientes.</p>
    </div>
    <a class="button gold" href="/sale.php">Nova venda</a>
</section>
<section class="panel table-wrap">
    <?php if ($sales): ?>
        <table>
            <thead>
            <tr><th>N.º</th><th>Data</th><th>Cliente</th><th>Peça</th><th>Qtd.</th><th>Pagamento</th><th>Total</th></tr>
            </thead>
            <tbody>
            <?php foreach ($sales as $sale): ?>
                <tr>
                    <td>#<?= (int) $sale['id'] ?></td>
                    <td><?= h(date('d/m/Y H:i', strtotime($sale['created_at']))) ?></td>
                    <td><?= h($sale['client']) ?></td>
                    <td><?= h($sale['part']) ?></td>
                    <td><?= (int) $sale['quantity'] ?></td>
                    <td><?= h($sale['payment']) ?></td>
                    <td><strong><?= money($sale['total']) ?></strong></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="empty">Ainda não existem vendas registadas.</p>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
