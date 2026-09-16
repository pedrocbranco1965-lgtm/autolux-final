<?php

/** @var array $encomenda */

use App\Support\Format;
?>

<div class="pagina-cabecalho">
    <div>
        <h1>Encomenda <?= e($encomenda['numero']) ?></h1>
        <p>Submetida a <?= e(Format::dataHora($encomenda['criadaEm'])) ?> · dados da API Node.js.</p>
    </div>
    <div class="acoes">
        <span class="<?= e(Format::classeEstado($encomenda['estado'])) ?>"><?= e($encomenda['estado']) ?></span>
        <a class="botao botao--secundario" href="/encomendas.php">Voltar às encomendas</a>
    </div>
</div>

<section class="grelha grelha--2">
    <article class="cartao">
        <h2>Fornecedor</h2>
        <p><strong><?= e($encomenda['fornecedorNome']) ?></strong></p>
        <p class="texto-suave">Entrega prevista: <?= e(Format::data($encomenda['dataPrevista'])) ?></p>
    </article>

    <article class="cartao">
        <h2>Resumo</h2>
        <p>Total: <strong><?= e(Format::moeda($encomenda['total'])) ?></strong></p>
        <?php if (!empty($encomenda['observacoes'])) : ?>
            <p class="texto-suave">Observações: <?= e($encomenda['observacoes']) ?></p>
        <?php endif; ?>
    </article>
</section>

<section class="cartao">
    <h2>Artigos encomendados</h2>
    <div class="tabela-wrapper">
        <table>
            <thead>
            <tr>
                <th>Referência</th>
                <th>Designação</th>
                <th class="numerico">Quantidade</th>
                <th class="numerico">Preço de custo</th>
                <th class="numerico">Subtotal</th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($encomenda['itens'] ?? [] as $item) : ?>
                <tr>
                    <td><?= e($item['referencia']) ?></td>
                    <td><?= e($item['designacao']) ?></td>
                    <td class="numerico"><?= e($item['quantidade']) ?></td>
                    <td class="numerico"><?= e(Format::moeda($item['precoUnitario'])) ?></td>
                    <td class="numerico"><?= e(Format::moeda($item['subtotal'])) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
            <tfoot>
            <tr>
                <th colspan="4" class="numerico">Total</th>
                <th class="numerico"><?= e(Format::moeda($encomenda['total'])) ?></th>
            </tr>
            </tfoot>
        </table>
    </div>
</section>
