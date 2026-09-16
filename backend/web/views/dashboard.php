<?php

/**
 * @var int $totalPecas
 * @var float $valorStock
 * @var int $totalClientes
 * @var int $totalVendas
 * @var float $vendasMes
 * @var array $ultimasVendas
 * @var array $pecasEmRutura
 * @var array|null $resumoCompras
 * @var string|null $erroApi
 */

use App\Support\Format;
?>

<div class="pagina-cabecalho">
    <div>
        <h1>Painel de gestão</h1>
        <p>Visão geral do armazém: stock, vendas do mês e situação das compras a fornecedores.</p>
    </div>
    <div class="acoes">
        <a class="botao" href="/nova-venda.php">Nova encomenda de cliente</a>
        <a class="botao botao--secundario" href="/encomendas.php">Encomendar a fornecedor</a>
    </div>
</div>

<section class="grelha grelha--4">
    <div class="indicador">
        <div class="indicador__valor"><?= e($totalPecas) ?></div>
        <div class="indicador__etiqueta">Peças em catálogo</div>
    </div>
    <div class="indicador">
        <div class="indicador__valor"><?= e(Format::moeda($valorStock)) ?></div>
        <div class="indicador__etiqueta">Valor do stock</div>
    </div>
    <div class="indicador">
        <div class="indicador__valor"><?= e($totalClientes) ?></div>
        <div class="indicador__etiqueta">Clientes ativos</div>
    </div>
    <div class="indicador">
        <div class="indicador__valor"><?= e(Format::moeda($vendasMes)) ?></div>
        <div class="indicador__etiqueta">Vendas deste mês (<?= e($totalVendas) ?> no total)</div>
    </div>
</section>

<section class="grelha grelha--2 espacamento-topo">
    <article class="cartao">
        <div class="cartao__titulo">
            <h2>Últimas vendas</h2>
            <a href="/vendas.php">Ver todas</a>
        </div>
        <?php if ($ultimasVendas === []) : ?>
            <p class="vazio">Ainda não existem vendas registadas.</p>
        <?php else : ?>
            <div class="tabela-wrapper">
                <table>
                    <thead>
                    <tr>
                        <th>Número</th>
                        <th>Cliente</th>
                        <th>Data</th>
                        <th class="numerico">Total</th>
                        <th>Estado</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($ultimasVendas as $venda) : ?>
                        <tr>
                            <td><?= e($venda['numero']) ?></td>
                            <td><?= e($venda['cliente']) ?></td>
                            <td><?= e(Format::data($venda['criada_em'])) ?></td>
                            <td class="numerico"><?= e(Format::moeda($venda['total'])) ?></td>
                            <td><span class="<?= e(Format::classeEstado($venda['estado'])) ?>"><?= e($venda['estado']) ?></span></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </article>

    <article class="cartao">
        <div class="cartao__titulo">
            <h2>Compras a fornecedores</h2>
            <a href="/encomendas.php">Gerir encomendas</a>
        </div>

        <?php if ($erroApi !== null) : ?>
            <div class="alerta alerta--erro">
                <strong>Serviço Node.js indisponível.</strong>
                <p><?= e($erroApi) ?></p>
                <p class="texto-suave">Arranque a solução completa com <code>npm start</code>.</p>
            </div>
        <?php else : ?>
            <p class="texto-suave">
                Dados obtidos da API Node.js (Base de Dados 2).
                Valor em curso: <strong><?= e(Format::moeda($resumoCompras['valorEmCurso'] ?? 0)) ?></strong>.
            </p>
            <div class="tabela-wrapper">
                <table>
                    <thead>
                    <tr>
                        <th>Estado</th>
                        <th class="numerico">Encomendas</th>
                        <th class="numerico">Valor</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach (($resumoCompras['porEstado'] ?? []) as $estado => $dados) : ?>
                        <tr>
                            <td><span class="<?= e(Format::classeEstado($estado)) ?>"><?= e($estado) ?></span></td>
                            <td class="numerico"><?= e($dados['total']) ?></td>
                            <td class="numerico"><?= e(Format::moeda($dados['valor'])) ?></td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </article>
</section>

<section class="cartao">
    <div class="cartao__titulo">
        <h2>Peças a precisar de reposição</h2>
        <a href="/encomendas.php">Encomendar</a>
    </div>
    <?php if ($pecasEmRutura === []) : ?>
        <p class="vazio">Não há peças abaixo do stock mínimo.</p>
    <?php else : ?>
        <div class="tabela-wrapper">
            <table>
                <thead>
                <tr>
                    <th>Referência</th>
                    <th>Designação</th>
                    <th>Marca</th>
                    <th class="numerico">Stock</th>
                    <th class="numerico">Mínimo</th>
                </tr>
                </thead>
                <tbody>
                <?php foreach ($pecasEmRutura as $peca) : ?>
                    <tr>
                        <td><?= e($peca['referencia']) ?></td>
                        <td><?= e($peca['designacao']) ?></td>
                        <td><?= e($peca['marca']) ?></td>
                        <td class="numerico"><span class="badge badge--aviso"><?= e($peca['stock']) ?></span></td>
                        <td class="numerico"><?= e($peca['stock_minimo']) ?></td>
                    </tr>
                <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
