<?php

/**
 * @var array $fornecedores
 * @var array $encomendas
 * @var array|null $resumo
 * @var string $estadoFiltro
 * @var int|null $fornecedorFiltro
 * @var array $formulario
 * @var array $erros
 * @var string|null $erroApi
 */

use App\Support\Csrf;
use App\Support\Format;

$estados = ['submetida', 'confirmada', 'recebida', 'cancelada'];
$proximoEstado = ['submetida' => 'confirmada', 'confirmada' => 'recebida'];
$fornecedorEscolhido = (int) old($formulario, 'fornecedor_id', $fornecedorFiltro ?? 0);
?>

<div class="pagina-cabecalho">
    <div>
        <h1>Encomendas a fornecedores</h1>
        <p>Submissão e acompanhamento de compras através da web API Node.js.</p>
    </div>
    <a class="botao botao--secundario" href="/fornecedores.php">Ver fornecedores</a>
</div>

<?php if ($erroApi !== null) : ?>
    <div class="alerta alerta--erro">
        <strong>Serviço de compras (Node.js) indisponível.</strong>
        <p><?= e($erroApi) ?></p>
        <p class="texto-suave">Arranque a solução completa com <code>npm start</code>.</p>
    </div>
<?php else : ?>

    <?php if ($erros !== []) : ?>
        <div class="alerta alerta--erro">
            <strong>Não foi possível concluir a operação:</strong>
            <ul>
                <?php foreach ($erros as $erro) : ?>
                    <li><?= e($erro) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <?php if ($resumo !== null) : ?>
        <section class="grelha grelha--4">
            <div class="indicador">
                <div class="indicador__valor"><?= e($resumo['totalEncomendas']) ?></div>
                <div class="indicador__etiqueta">Encomendas registadas</div>
            </div>
            <?php foreach (['submetida', 'confirmada', 'recebida'] as $estado) : ?>
                <div class="indicador">
                    <div class="indicador__valor"><?= e($resumo['porEstado'][$estado]['total'] ?? 0) ?></div>
                    <div class="indicador__etiqueta">
                        <?= e(ucfirst($estado)) ?> ·
                        <?= e(Format::moeda($resumo['porEstado'][$estado]['valor'] ?? 0)) ?>
                    </div>
                </div>
            <?php endforeach; ?>
        </section>
    <?php endif; ?>

    <section class="cartao espacamento-topo">
        <h2>Nova encomenda</h2>
        <p class="texto-suave">
            Ao escolher o fornecedor, o catálogo é carregado de forma assíncrona
            (<code>GET /api/fornecedores/:id/artigos</code>). A submissão usa
            <code>POST /api/encomendas</code>.
        </p>

        <form class="formulario" method="post" action="/encomendas.php">
            <?= Csrf::campo() ?>
            <input type="hidden" name="acao" value="submeter">

            <div class="linha-campos">
                <div class="campo">
                    <label for="fornecedor_id">Fornecedor *</label>
                    <select id="fornecedor_id" name="fornecedor_id" required>
                        <option value="">— Selecione o fornecedor —</option>
                        <?php foreach ($fornecedores as $fornecedor) : ?>
                            <option value="<?= e($fornecedor['id']) ?>"
                                <?= $fornecedorEscolhido === (int) $fornecedor['id'] ? 'selected' : '' ?>>
                                <?= e($fornecedor['nome']) ?> (<?= e($fornecedor['pais']) ?>,
                                <?= e($fornecedor['prazoEntregaDias']) ?> dias)
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="campo">
                    <label for="data_prevista">Data prevista de entrega</label>
                    <input type="date" id="data_prevista" name="data_prevista"
                           value="<?= e(old($formulario, 'data_prevista')) ?>">
                </div>

                <div class="campo">
                    <label for="observacoes">Observações</label>
                    <input type="text" id="observacoes" name="observacoes"
                           placeholder="Ex.: entregar na doca 2"
                           value="<?= e(old($formulario, 'observacoes')) ?>">
                </div>
            </div>

            <div class="linha-campos">
                <div class="campo">
                    <label for="seletor-artigo">Artigo do fornecedor</label>
                    <select id="seletor-artigo">
                        <option value="">— Selecione o artigo —</option>
                    </select>
                    <small id="estado-artigos">Escolha primeiro o fornecedor para carregar o catálogo.</small>
                </div>
                <div class="campo">
                    <label for="quantidade-artigo">Quantidade</label>
                    <input type="number" id="quantidade-artigo" min="1" value="1">
                </div>
                <div class="campo" style="justify-content:flex-end">
                    <button class="botao botao--secundario" type="button" id="adicionar-artigo">
                        Adicionar à encomenda
                    </button>
                </div>
            </div>

            <div class="tabela-wrapper oculto" id="tabela-encomenda">
                <table>
                    <thead>
                    <tr>
                        <th>Referência</th>
                        <th>Designação</th>
                        <th class="numerico">Quantidade</th>
                        <th class="numerico">Preço de custo</th>
                        <th class="numerico">Subtotal</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody id="linhas-encomenda"></tbody>
                    <tfoot>
                    <tr>
                        <th colspan="4" class="numerico">Total da encomenda</th>
                        <th class="numerico" id="total-encomenda">0,00 €</th>
                        <th></th>
                    </tr>
                    </tfoot>
                </table>
            </div>

            <div class="acoes espacamento-topo">
                <button class="botao" type="submit">Submeter encomenda</button>
            </div>
        </form>
    </section>

    <form class="cartao" method="get" action="/encomendas.php">
        <div class="linha-campos">
            <div class="campo">
                <label for="estado">Filtrar por estado</label>
                <select id="estado" name="estado">
                    <option value="">Todos os estados</option>
                    <?php foreach ($estados as $estado) : ?>
                        <option value="<?= e($estado) ?>" <?= $estadoFiltro === $estado ? 'selected' : '' ?>>
                            <?= e(ucfirst($estado)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo">
                <label for="filtro_fornecedor">Filtrar por fornecedor</label>
                <select id="filtro_fornecedor" name="fornecedor_id">
                    <option value="">Todos os fornecedores</option>
                    <?php foreach ($fornecedores as $fornecedor) : ?>
                        <option value="<?= e($fornecedor['id']) ?>"
                            <?= $fornecedorFiltro === (int) $fornecedor['id'] ? 'selected' : '' ?>>
                            <?= e($fornecedor['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo" style="justify-content:flex-end">
                <div class="acoes">
                    <button class="botao" type="submit">Filtrar</button>
                    <a class="botao botao--secundario" href="/encomendas.php">Limpar</a>
                </div>
            </div>
        </div>
    </form>

    <section class="cartao">
        <div class="cartao__titulo">
            <h2><?= e(count($encomendas)) ?> encomenda(s)</h2>
            <span class="texto-suave">GET /api/encomendas</span>
        </div>

        <?php if ($encomendas === []) : ?>
            <p class="vazio">Ainda não existem encomendas para os critérios escolhidos.</p>
        <?php else : ?>
            <div class="tabela-wrapper">
                <table>
                    <thead>
                    <tr>
                        <th>Número</th>
                        <th>Fornecedor</th>
                        <th>Submetida</th>
                        <th>Prevista</th>
                        <th class="numerico">Artigos</th>
                        <th class="numerico">Total</th>
                        <th>Estado</th>
                        <th>Ações</th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($encomendas as $encomenda) : ?>
                        <tr>
                            <td><?= e($encomenda['numero']) ?></td>
                            <td><?= e($encomenda['fornecedorNome']) ?></td>
                            <td><?= e(Format::data($encomenda['criadaEm'])) ?></td>
                            <td><?= e(Format::data($encomenda['dataPrevista'])) ?></td>
                            <td class="numerico"><?= e($encomenda['totalArtigos']) ?></td>
                            <td class="numerico"><?= e(Format::moeda($encomenda['total'])) ?></td>
                            <td>
                                <span class="<?= e(Format::classeEstado($encomenda['estado'])) ?>">
                                    <?= e($encomenda['estado']) ?>
                                </span>
                            </td>
                            <td>
                                <div class="acoes">
                                    <a class="botao botao--pequeno botao--secundario"
                                       href="/encomenda.php?id=<?= e($encomenda['id']) ?>">Detalhe</a>

                                    <?php if (isset($proximoEstado[$encomenda['estado']])) : ?>
                                        <form method="post" action="/encomendas.php">
                                            <?= Csrf::campo() ?>
                                            <input type="hidden" name="acao" value="atualizar_estado">
                                            <input type="hidden" name="encomenda_id" value="<?= e($encomenda['id']) ?>">
                                            <input type="hidden" name="estado"
                                                   value="<?= e($proximoEstado[$encomenda['estado']]) ?>">
                                            <button class="botao botao--pequeno" type="submit">
                                                Marcar <?= e($proximoEstado[$encomenda['estado']]) ?>
                                            </button>
                                        </form>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>
