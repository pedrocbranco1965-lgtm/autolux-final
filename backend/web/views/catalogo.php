<?php

/**
 * @var array $pecas
 * @var array $filtros
 * @var array $marcas
 * @var array $tipos
 * @var array $precos
 * @var bool $temFiltros
 */

use App\Support\Format;

$ordens = [
    'designacao' => 'Designação (A-Z)',
    'preco_asc' => 'Preço mais baixo',
    'preco_desc' => 'Preço mais alto',
    'stock_asc' => 'Menor stock',
    'marca' => 'Marca',
];
?>

<div class="pagina-cabecalho">
    <div>
        <h1>Catálogo de peças</h1>
        <p>Peças obtidas da Base de Dados 1 (MySQL). Use os filtros para encontrar material em stock.</p>
    </div>
    <a class="botao" href="/nova-venda.php">Encomendar para cliente</a>
</div>

<form class="cartao" method="get" action="/catalogo.php">
    <div class="linha-campos">
        <div class="campo">
            <label for="pesquisa">Pesquisar</label>
            <input type="search" id="pesquisa" name="pesquisa" placeholder="Designação ou referência"
                   value="<?= e($filtros['pesquisa']) ?>">
        </div>

        <div class="campo">
            <label for="marca_id">Marca</label>
            <select id="marca_id" name="marca_id">
                <option value="">Todas as marcas</option>
                <?php foreach ($marcas as $marca) : ?>
                    <option value="<?= e($marca['id']) ?>" <?= $filtros['marca_id'] === (int) $marca['id'] ? 'selected' : '' ?>>
                        <?= e($marca['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo">
            <label for="tipo_id">Tipo de peça</label>
            <select id="tipo_id" name="tipo_id">
                <option value="">Todos os tipos</option>
                <?php foreach ($tipos as $tipo) : ?>
                    <option value="<?= e($tipo['id']) ?>" <?= $filtros['tipo_id'] === (int) $tipo['id'] ? 'selected' : '' ?>>
                        <?= e($tipo['nome']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="campo">
            <label for="preco_min">Preço mínimo (€)</label>
            <input type="number" id="preco_min" name="preco_min" min="0" step="0.01"
                   placeholder="<?= e(number_format($precos['minimo'], 2, '.', '')) ?>"
                   value="<?= e($filtros['preco_min']) ?>">
        </div>

        <div class="campo">
            <label for="preco_max">Preço máximo (€)</label>
            <input type="number" id="preco_max" name="preco_max" min="0" step="0.01"
                   placeholder="<?= e(number_format($precos['maximo'], 2, '.', '')) ?>"
                   value="<?= e($filtros['preco_max']) ?>">
        </div>

        <div class="campo">
            <label for="ordem">Ordenar por</label>
            <select id="ordem" name="ordem">
                <?php foreach ($ordens as $chave => $etiqueta) : ?>
                    <option value="<?= e($chave) ?>" <?= $filtros['ordem'] === $chave ? 'selected' : '' ?>>
                        <?= e($etiqueta) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>

    <div class="acoes espacamento-topo">
        <label class="texto-suave">
            <input type="checkbox" name="apenas_stock" value="1" style="width:auto"
                <?= $filtros['apenas_stock'] ? 'checked' : '' ?>>
            Mostrar apenas peças com stock
        </label>
        <button class="botao" type="submit">Aplicar filtros</button>
        <?php if ($temFiltros) : ?>
            <a class="botao botao--secundario" href="/catalogo.php">Limpar filtros</a>
        <?php endif; ?>
    </div>
</form>

<p class="texto-suave">
    <?= e(count($pecas)) ?> peça(s) encontrada(s)<?= $temFiltros ? ' com os filtros aplicados' : '' ?>.
</p>

<?php if ($pecas === []) : ?>
    <div class="cartao">
        <p class="vazio">Nenhuma peça corresponde aos filtros escolhidos.</p>
    </div>
<?php else : ?>
    <section class="grelha grelha--3">
        <?php foreach ($pecas as $peca) : ?>
            <article class="peca">
                <span class="peca__referencia"><?= e($peca['referencia']) ?></span>
                <span class="peca__designacao"><?= e($peca['designacao']) ?></span>
                <div class="etiquetas">
                    <span class="etiqueta"><?= e($peca['marca']) ?></span>
                    <span class="etiqueta"><?= e($peca['tipo']) ?></span>
                </div>
                <p class="peca__descricao"><?= e($peca['descricao']) ?></p>
                <div class="peca__rodape">
                    <span class="peca__preco"><?= e(Format::moeda($peca['preco'])) ?></span>
                    <?php if ((int) $peca['stock'] === 0) : ?>
                        <span class="badge badge--erro">Esgotada</span>
                    <?php elseif ((int) $peca['stock'] <= (int) $peca['stock_minimo']) : ?>
                        <span class="badge badge--aviso"><?= e($peca['stock']) ?> em stock</span>
                    <?php else : ?>
                        <span class="badge badge--ok"><?= e($peca['stock']) ?> em stock</span>
                    <?php endif; ?>
                </div>
                <?php if ((int) $peca['stock'] > 0) : ?>
                    <a class="botao botao--pequeno" href="/nova-venda.php?peca_id=<?= e($peca['id']) ?>">
                        Encomendar esta peça
                    </a>
                <?php endif; ?>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
