<?php

/** @var array $venda */

use App\Support\Format;
?>

<div class="pagina-cabecalho">
    <div>
        <h1>Venda <?= e($venda['numero']) ?></h1>
        <p>Registada a <?= e(Format::dataHora($venda['criada_em'])) ?>.</p>
    </div>
    <div class="acoes">
        <span class="<?= e(Format::classeEstado($venda['estado'])) ?>"><?= e($venda['estado']) ?></span>
        <a class="botao botao--secundario" href="/vendas.php">Voltar às vendas</a>
    </div>
</div>

<section class="grelha grelha--2">
    <article class="cartao">
        <h2>Cliente</h2>
        <p><strong><?= e($venda['cliente']) ?></strong></p>
        <p class="texto-suave"><?= e($venda['cliente_email']) ?></p>
    </article>

    <article class="cartao">
        <h2>Pagamento</h2>
        <p><strong><?= e($venda['metodo_pagamento']) ?></strong></p>
        <p class="texto-suave">
            Referência: <?= e($venda['referencia_pagamento'] ?: '—') ?>
        </p>
        <?php if (!empty($venda['observacoes'])) : ?>
            <p class="texto-suave">Observações: <?= e($venda['observacoes']) ?></p>
        <?php endif; ?>
    </article>
</section>

<section class="cartao">
    <h2>Linhas da encomenda</h2>
    <div class="tabela-wrapper">
        <table>
            <thead>
            <tr>
                <th>Referência</th>
                <th>Designação</th>
                <th class="numerico">Quantidade</th>
                <th class="numerico">Preço unitário</th>
                <th class="numerico">Subtotal</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($venda['itens'] as $item) : ?>
                <tr>
                    <td><?= e($item['referencia']) ?></td>
                    <td><?= e($item['designacao']) ?></td>
                    <td class="numerico"><?= e($item['quantidade']) ?></td>
                    <td class="numerico"><?= e(Format::moeda($item['preco_unitario'])) ?></td>
                    <td class="numerico"><?= e(Format::moeda($item['subtotal'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="4" class="numerico">Total</th>
                <th class="numerico"><?= e(Format::moeda($venda['total'])) ?></th>
            </tr>
            </tfoot>
        </table>
    </div>
</section>
