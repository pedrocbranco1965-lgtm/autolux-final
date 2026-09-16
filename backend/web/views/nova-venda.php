<?php

/**
 * @var array $clientes
 * @var array $pecas
 * @var array $metodos
 * @var array $camposPorMetodo
 * @var int $pecaSelecionada
 * @var array $formulario
 * @var array $erros
 */

use App\Support\Csrf;
use App\Support\Format;

$clienteEscolhido = (int) old($formulario, 'cliente_id', 0);
$metodoEscolhido = (int) old($formulario, 'metodo_pagamento_id', $metodos[0]['id'] ?? 0);
$dadosPagamento = old($formulario, 'pagamento', []);
?>

<div class="pagina-cabecalho">
    <div>
        <h1>Nova encomenda de cliente</h1>
        <p>Registo de venda de peças: o stock é abatido e o pagamento é tratado pelo método escolhido.</p>
    </div>
    <a class="botao botao--secundario" href="/catalogo.php">Ver catálogo</a>
</div>

<?php if ($erros !== []) : ?>
    <div class="alerta alerta--erro">
        <strong>Não foi possível registar a encomenda:</strong>
        <ul>
            <?php foreach ($erros as $erro) : ?>
                <li><?= e($erro) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form class="formulario" method="post" action="/nova-venda.php" id="formulario-venda">
    <?= Csrf::campo() ?>

    <section class="cartao">
        <h2>1. Cliente</h2>
        <div class="campo">
            <label for="cliente_id">Cliente</label>
            <select id="cliente_id" name="cliente_id" required>
                <option value="">— Selecione o cliente —</option>
                <?php foreach ($clientes as $cliente) : ?>
                    <option value="<?= e($cliente['id']) ?>" <?= $clienteEscolhido === (int) $cliente['id'] ? 'selected' : '' ?>>
                        <?= e($cliente['nome']) ?> (NIF <?= e($cliente['nif']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
            <small>Só aparecem clientes ativos. <a href="/clientes.php">Registar novo cliente</a>.</small>
        </div>
    </section>

    <section class="cartao">
        <h2>2. Peças a encomendar</h2>

        <div class="linha-campos">
            <div class="campo">
                <label for="seletor-peca">Peça</label>
                <select id="seletor-peca" name="itens[0][peca_id]" data-info-peca>
                    <option value="">— Selecione a peça —</option>
                    <?php foreach ($pecas as $peca) : ?>
                        <option value="<?= e($peca['id']) ?>"
                                data-preco="<?= e($peca['preco']) ?>"
                                data-stock="<?= e($peca['stock']) ?>"
                                data-marca="<?= e($peca['marca']) ?>"
                                data-tipo="<?= e($peca['tipo']) ?>"
                                data-referencia="<?= e($peca['referencia']) ?>"
                                data-designacao="<?= e($peca['designacao']) ?>"
                            <?= $pecaSelecionada === (int) $peca['id'] ? 'selected' : '' ?>>
                            <?= e($peca['referencia']) ?> — <?= e($peca['designacao']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="campo">
                <label for="seletor-quantidade">Quantidade</label>
                <input type="number" id="seletor-quantidade" name="itens[0][quantidade]" min="1" value="1">
            </div>

            <div class="campo" style="justify-content:flex-end">
                <button class="botao botao--secundario" type="button" id="adicionar-peca">
                    Adicionar outra peça
                </button>
            </div>
        </div>

        <small class="texto-suave">
            A peça que ficar selecionada acima é incluída na encomenda ao submeter.
            Use "Adicionar outra peça" apenas para encomendar várias peças de uma só vez.
        </small>

        <div class="info-peca espacamento-topo" id="info-peca" data-vazio="Selecione uma peça para ver os detalhes.">
            Selecione uma peça para ver os detalhes.
        </div>

        <div class="tabela-wrapper espacamento-topo oculto" id="tabela-itens">
            <table>
                <thead>
                <tr>
                    <th>Referência</th>
                    <th>Designação</th>
                    <th class="numerico">Quantidade</th>
                    <th class="numerico">Preço unitário</th>
                    <th class="numerico">Subtotal</th>
                    <th></th>
                </tr>
                </thead>
                <tbody id="linhas-itens"></tbody>
                <tfoot>
                <tr>
                    <th colspan="4" class="numerico">Total das linhas adicionadas</th>
                    <th class="numerico" id="total-itens">0,00 €</th>
                    <th></th>
                </tr>
                </tfoot>
            </table>
        </div>
    </section>

    <section class="cartao">
        <h2>3. Tipo de pagamento</h2>
        <div class="campo">
            <label for="metodo_pagamento_id">Método de pagamento</label>
            <select id="metodo_pagamento_id" name="metodo_pagamento_id" required>
                <?php foreach ($metodos as $metodo) : ?>
                    <option value="<?= e($metodo['id']) ?>" <?= $metodoEscolhido === (int) $metodo['id'] ? 'selected' : '' ?>>
                        <?= e($metodo['designacao']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
            <small>
                Os métodos vêm da tabela <code>metodos_pagamento</code>: para acrescentar um novo basta
                inserir uma linha, sem alterar esta página.
            </small>
        </div>

        <?php foreach ($camposPorMetodo as $metodoId => $definicao) : ?>
            <div class="bloco-pagamento<?= $metodoEscolhido === (int) $metodoId ? '' : ' oculto' ?>"
                 data-metodo="<?= e($metodoId) ?>">
                <?php if (!empty($definicao['descricao'])) : ?>
                    <p class="texto-suave"><?= e($definicao['descricao']) ?></p>
                <?php endif; ?>

                <?php foreach ($definicao['campos'] as $campo) : ?>
                    <div class="campo">
                        <label for="pagamento_<?= e($campo['nome']) ?>">
                            <?= e($campo['etiqueta']) ?><?= $campo['obrigatorio'] ? ' *' : '' ?>
                        </label>
                        <input type="<?= e($campo['tipo']) ?>"
                               id="pagamento_<?= e($campo['nome']) ?>"
                               name="pagamento[<?= e($campo['nome']) ?>]"
                               <?= isset($campo['tipo']) && $campo['tipo'] === 'number' ? 'step="0.01" min="0"' : '' ?>
                               placeholder="<?= e($campo['exemplo'] ?? '') ?>"
                               value="<?= e($dadosPagamento[$campo['nome']] ?? '') ?>">
                        <?php if (!empty($campo['ajuda'])) : ?>
                            <small><?= e($campo['ajuda']) ?></small>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endforeach; ?>
    </section>

    <section class="cartao">
        <h2>4. Observações</h2>
        <div class="campo">
            <label for="observacoes">Notas para o armazém (opcional)</label>
            <textarea id="observacoes" name="observacoes" rows="2"
                      placeholder="Ex.: entrega em mão, faturar a 30 dias..."><?= e(old($formulario, 'observacoes')) ?></textarea>
        </div>

        <div class="acoes espacamento-topo">
            <button class="botao" type="submit">Registar encomenda</button>
            <a class="botao botao--secundario" href="/vendas.php">Ver vendas registadas</a>
        </div>
    </section>
</form>

<p class="texto-suave">
    Peças disponíveis com stock: <?= e(count($pecas)) ?> ·
    Valor médio do catálogo disponível:
    <?= e(Format::moeda($pecas === [] ? 0 : array_sum(array_column($pecas, 'preco')) / count($pecas))) ?>
</p>
