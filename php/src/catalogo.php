<?php
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/db.php';

$marca = trim($_GET['marca'] ?? '');
$tipo = trim($_GET['tipo'] ?? '');
$precoMax = trim($_GET['preco_max'] ?? '');

$where = [];
$params = [];

if ($marca !== '') {
    $where[] = 'marca = :marca';
    $params['marca'] = $marca;
}

if ($tipo !== '') {
    $where[] = 'tipo = :tipo';
    $params['tipo'] = $tipo;
}

if ($precoMax !== '' && is_numeric($precoMax)) {
    $where[] = 'preco <= :preco_max';
    $params['preco_max'] = $precoMax;
}

$sql = 'SELECT * FROM pecas';
if ($where) {
    $sql .= ' WHERE ' . implode(' AND ', $where);
}
$sql .= ' ORDER BY marca, nome';

$stmt = db()->prepare($sql);
$stmt->execute($params);
$pecas = $stmt->fetchAll();

$marcas = db()->query('SELECT DISTINCT marca FROM pecas ORDER BY marca')->fetchAll();
$tipos = db()->query('SELECT DISTINCT tipo FROM pecas ORDER BY tipo')->fetchAll();

render_header('Catalogo');
?>

<h1>Catalogo de Pecas</h1>
<p class="muted">Lista obtida diretamente da base de dados MySQL de vendas e stock.</p>

<form class="filters" method="get">
    <label>
        Marca
        <select name="marca">
            <option value="">Todas</option>
            <?php foreach ($marcas as $option): ?>
                <option value="<?= e($option['marca']) ?>" <?= $marca === $option['marca'] ? 'selected' : '' ?>>
                    <?= e($option['marca']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Tipo de peca
        <select name="tipo">
            <option value="">Todos</option>
            <?php foreach ($tipos as $option): ?>
                <option value="<?= e($option['tipo']) ?>" <?= $tipo === $option['tipo'] ? 'selected' : '' ?>>
                    <?= e($option['tipo']) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Preco maximo
        <input type="number" min="0" step="0.01" name="preco_max" value="<?= e($precoMax) ?>" placeholder="Ex: 50">
    </label>

    <button class="button" type="submit">Filtrar</button>
    <a class="button secondary" href="/catalogo.php">Limpar</a>
</form>

<section class="parts-grid">
    <?php if (!$pecas): ?>
        <p class="notice">Nenhuma peca encontrada com os filtros escolhidos.</p>
    <?php endif; ?>

    <?php foreach ($pecas as $peca): ?>
        <article class="part-card">
            <div class="part-top">
                <span><?= e($peca['referencia']) ?></span>
                <strong><?= money($peca['preco']) ?></strong>
            </div>
            <h2><?= e($peca['nome']) ?></h2>
            <p><?= e($peca['descricao']) ?></p>
            <dl>
                <div><dt>Marca</dt><dd><?= e($peca['marca']) ?></dd></div>
                <div><dt>Tipo</dt><dd><?= e($peca['tipo']) ?></dd></div>
                <div><dt>Stock</dt><dd><?= e((string) $peca['stock']) ?></dd></div>
            </dl>
            <a class="button small" href="/venda.php?peca_id=<?= e((string) $peca['id']) ?>">Vender</a>
        </article>
    <?php endforeach; ?>
</section>

<?php render_footer(); ?>
