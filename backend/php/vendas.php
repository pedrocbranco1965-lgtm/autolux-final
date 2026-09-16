<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

$vendas = (new VendaServico(Database::vendas()))->todas();
$nomesPagamento = [];
foreach (CatalogoPagamentos::todos() as $metodo) {
    $nomesPagamento[$metodo->codigo()] = $metodo->nome();
}

layout_inicio('Vendas', 'vendas');
?>
<section class="hero">
    <div>
        <p class="eyebrow">Base de Dados 1</p>
        <h1>Vendas registadas</h1>
        <p class="muted">Cada venda abate stock e guarda o método de pagamento escolhido.</p>
    </div>
    <a class="btn btn-primary" href="venda.php">Nova venda</a>
</section>

<?php if (!$vendas): ?>
    <p class="muted">Ainda não existem vendas.</p>
<?php else: ?>
    <div class="table-wrap card">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Data</th>
                    <th>Cliente</th>
                    <th>Peça</th>
                    <th>Qtd</th>
                    <th>Total</th>
                    <th>Pagamento</th>
                    <th>Notas</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendas as $venda): ?>
                    <tr>
                        <td><?= (int) $venda['id'] ?></td>
                        <td><?= e($venda['criado_em']) ?></td>
                        <td><?= e($venda['cliente_nome']) ?></td>
                        <td><?= e($venda['referencia']) ?> · <?= e($venda['peca_nome']) ?></td>
                        <td><?= (int) $venda['quantidade'] ?></td>
                        <td><?= dinheiro($venda['total']) ?></td>
                        <td><?= e($nomesPagamento[$venda['tipo_pagamento']] ?? $venda['tipo_pagamento']) ?></td>
                        <td class="notas"><?= e($venda['notas_pagamento']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php layout_fim(); ?>
