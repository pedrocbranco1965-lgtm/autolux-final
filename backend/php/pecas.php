<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

$repo = PecaFactory::criar();
$marca = isset($_GET['marca']) && $_GET['marca'] !== '' ? (string) $_GET['marca'] : null;
$tipo = isset($_GET['tipo']) && $_GET['tipo'] !== '' ? (string) $_GET['tipo'] : null;
$precoMin = isset($_GET['preco_min']) && $_GET['preco_min'] !== '' ? (float) $_GET['preco_min'] : null;
$precoMax = isset($_GET['preco_max']) && $_GET['preco_max'] !== '' ? (float) $_GET['preco_max'] : null;

$pecas = $repo->listar($marca, $tipo, $precoMin, $precoMax);
$marcas = $repo->marcas();
$tipos = $repo->tipos();

layout_inicio('Catálogo de peças', 'pecas');
?>
<section class="hero">
    <div>
        <p class="eyebrow">Requisito A</p>
        <h1>Catálogo de peças</h1>
        <p class="muted">Listagem obtida da fonte <?= e(fonte_dados_nome()) ?>, com filtros por marca, tipo e gama de preço.</p>
    </div>
    <span class="chip"><?= count($pecas) ?> resultado(s)</span>
</section>

<form class="filtros card" method="get">
    <label>
        Marca
        <select name="marca">
            <option value="">Todas</option>
            <?php foreach ($marcas as $opcao): ?>
                <option value="<?= e($opcao) ?>" <?= $marca === $opcao ? 'selected' : '' ?>><?= e($opcao) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>
        Tipo de peça
        <select name="tipo">
            <option value="">Todos</option>
            <?php foreach ($tipos as $opcao): ?>
                <option value="<?= e($opcao) ?>" <?= $tipo === $opcao ? 'selected' : '' ?>><?= e($opcao) ?></option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>
        Preço mínimo (€)
        <input type="number" step="0.01" min="0" name="preco_min" value="<?= $precoMin !== null ? e((string) $precoMin) : '' ?>">
    </label>
    <label>
        Preço máximo (€)
        <input type="number" step="0.01" min="0" name="preco_max" value="<?= $precoMax !== null ? e((string) $precoMax) : '' ?>">
    </label>
    <div class="filtros-acoes">
        <button class="btn btn-primary" type="submit">Filtrar</button>
        <a class="btn btn-ghost" href="pecas.php">Limpar</a>
    </div>
</form>

<?php if (!$pecas): ?>
    <p class="muted">Nenhuma peça corresponde aos filtros.</p>
<?php else: ?>
    <section class="pecas-grid">
        <?php foreach ($pecas as $peca): ?>
            <article class="peca-card">
                <img src="<?= e($peca['imagem']) ?>" alt="<?= e($peca['nome']) ?>">
                <div class="peca-body">
                    <p class="eyebrow"><?= e($peca['marca']) ?> · <?= e($peca['tipo']) ?></p>
                    <h2><?= e($peca['nome']) ?></h2>
                    <p class="ref">Ref. <?= e($peca['referencia']) ?></p>
                    <p class="muted"><?= e($peca['descricao']) ?></p>
                    <div class="peca-meta">
                        <strong><?= dinheiro($peca['preco']) ?></strong>
                        <span class="stock <?= (int) $peca['stock'] <= 3 ? 'esgotar' : '' ?>">
                            <?= (int) $peca['stock'] ?> un. em stock
                        </span>
                    </div>
                    <a class="btn btn-primary" href="venda.php?peca_id=<?= (int) $peca['id'] ?>">Vender esta peça</a>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php endif; ?>
<?php layout_fim(); ?>
