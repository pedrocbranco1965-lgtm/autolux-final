<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/functions.php';

$clients = salesDb()->query(
    `SELECT c.id, c.name, c.email, c.phone, c.tax_number,
            COUNT(s.id) AS salesCount, COALESCE(SUM(s.total), 0) AS totalSpent
     FROM clients c
     LEFT JOIN sales s ON s.client_id = c.id
     GROUP BY c.id
     ORDER BY c.name`
)->fetchAll();

$pageTitle = 'Clientes';
$activePage = 'clients';
require __DIR__ . '/../src/partials/header.php';
?>
<section class="page-heading">
    <div>
        <h1>Clientes</h1>
        <p class="subtitle">Consulta dos clientes e respetivo histórico resumido.</p>
    </div>
</section>
<section class="panel table-wrap">
    <table>
        <thead>
        <tr><th>Nome</th><th>Contacto</th><th>NIF</th><th>Vendas</th><th>Total</th></tr>
        </thead>
        <tbody>
        <?php foreach ($clients as $client): ?>
            <tr>
                <td><strong><?= h($client['name']) ?></strong></td>
                <td><?= h($client['email']) ?><br><span class="muted"><?= h($client['phone']) ?></span></td>
                <td><?= h($client['tax_number']) ?></td>
                <td><?= (int) $client['salesCount'] ?></td>
                <td><?= money($client['totalSpent']) ?></td>
            </tr>
        <?php endforeach; ?>
        </tbody>
    </table>
</section>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
