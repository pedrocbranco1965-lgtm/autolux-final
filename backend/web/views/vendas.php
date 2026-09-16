<?php

/**
 * @var array $vendas
 * @var array $clientes
 * @var int|null $clienteId
 * @var float $totalFaturado
 */

use App\Support\Format;
?>

<div class="pagina-cabecalho">
    <div>
        <h1>Vendas</h1>
        <p>Encomendas de peças registadas para clientes, com o respetivo método de pagamento.</p>
    </div>
    <a class="botao" href="/nova-venda.php">Nova encomenda</a>
</div>

<form class="cartao" method="get" action="/vendas.php">
    <div class="linha-campos">
        <div class="campo">
            <label for="cliente_id">Filtrar por cliente</label>
            <select id="cliente_id" name="cliente_id" onchange="this.form.submit()">
                <option value="">Todos os clientes</option>
                <?php foreach ($clientes as $cliente) : ?>
                    <option value="<?= e($cliente['id']) ?>" <?= $clienteId === (int) $cliente['id'] ? 'selected' : '' ?>>
                        <?= e($cliente['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="campo" style="justify-content:flex-end">
            <div class="acoes">
                <button class="botao" type="submit">Filtrar</button>
                <?php if ($clienteId !== null) : ?>
                    <a class="botao botao--secundario" href="/vendas.php">Limpar</a>
                <?php endif; ?>
            </div>
        </div>
    </div>
</form>

<section class="cartao">
    <div class="cartao__titulo">
        <h2><?= e(count($vendas)) ?> venda(s)</h2>
        <span class="texto-suave">Total faturado: <strong><?= e(Format::moeda($totalFaturado)) ?></strong></span>
    </div>

    <?php if ($vendas === []) : ?>
        <p class="vazio">Ainda não há vendas para os critérios escolhidos.</p>
    <?php else : ?>
        <div class="tabela-wrapper">
            <table>
                <thead>
                <tr>
                    <th>Número</th>
                    <th>Data</th>
                    <th>Cliente</th>
                    <th>Pagamento</th>
                    <th class="numerico">Artigos</th>
                    <th class="numerico">Total</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($vendas as $venda) : ?>
                    <tr>
                        <td><?= e($venda['numero']) ?></td>
                        <td><?= e(Format::dataHora($venda['criada_em'])) ?></td>
                        <td><?= e($venda['cliente']) ?></td>
                        <td><?= e($venda['metodo_pagamento']) ?></td>
                        <td class="numerico"><?= e($venda['total_artigos']) ?></td>
                        <td class="numerico"><?= e(Format::moeda($venda['total'])) ?></td>
                        <td><span class="<?= e(Format::classeEstado($venda['estado'])) ?>"><?= e($venda['estado']) ?></span></td>
                        <td class="numerico">
                            <a class="botao botao--pequeno botao--secundario" href="/venda.php?id=<?= e($venda['id']) ?>">
                                Detalhe
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
