<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/functions.php';

$db = salesDb();
$brand = trim((string) ($_GET['brand'] ?? ''));
$type = trim((string) ($_GET['type'] ?? ''));
$search = trim((string) ($_GET['search'] ?? ''));
$maxPrice = filter_input(INPUT_GET, 'max_price', FILTER_VALIDATE_FLOAT);

$conditions = [];
$parameters = [];
if ($brand !== '') {
    $conditions[] = 'brand = ?';
    $parameters[] = $brand;
}
if ($type !== '') {
    $conditions[] = 'type = ?';
    $parameters[] = $type;
}
if ($maxPrice !== false && $maxPrice !== null && $maxPrice >= 0) {
    $conditions[] = 'price <= ?';
    $parameters[] = $maxPrice;
}
if ($search !== '') {
    $conditions[] = '(name LIKE ? OR sku LIKE ?)';
    $parameters[] = "%{$search}%";
    $parameters[] = "%{$search}%";
}

$sql = 'SELECT * FROM parts';
if ($conditions) {
    $sql .= ' WHERE ' . implode(' AND ', $conditions);
}
$sql .= ' ORDER BY name';
$statement = $db->prepare($sql);
$statement->execute($parameters);
$parts = $statement->fetchAll();
$brands = $db->query('SELECT DISTINCT brand FROM parts ORDER BY brand')->fetchAll(PDO::FETCH_COLUMN);
$types = $db->query('SELECT DISTINCT type FROM parts ORDER BY type')->fetchAll(PDO::FETCH_COLUMN);

$pageTitle = 'Catálogo';
$activePage = 'catalog';
require __DIR__ . '/../src/partials/header.php';
?>
<section class="page-heading">
    <div>
        <h1>Catálogo de peças</h1>
        <p class="subtitle"><?= count($parts) ?> resultado(s) encontrado(s).</p>
    </div>
</section>

<form class="panel filters" method="get">
    <label>Pesquisar
        <input type="search" name="search" value="<?= h($search) ?>" placeholder="Nome ou SKU">
    </label>
    <label>Marca
        <select name="brand">
            <option value="">Todas</option>
            <?php foreach ($brands as $option): ?>
                <option value="<?= h($option) ?>" <?= $brand === $option ? 'selected' : '' ?>>
                    <?= h($option) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Tipo
        <select name="type">
            <option value="">Todos</option>
            <?php foreach ($types as $option): ?>
                <option value="<?= h($option) ?>" <?= $type === $option ? 'selected' : '' ?>>
                    <?= h($option) ?>
                </option>
            <?php endforeach; ?>
        </select>
    </label>
    <label>Preço máximo
        <input type="number" min="0" step="0.01" name="max_price"
               value="<?= $maxPrice !== false && $maxPrice !== null ? h((string) $maxPrice) : '' ?>">
    </label>
    <button class="button" type="submit">Filtrar</button>
</form>

<?php if ($parts): ?>
    <section class="parts-grid">
        <?php foreach ($parts as $part): ?>
            <article class="card part-card">
                <img src="<?= h($part['image_url']) ?>" alt="" loading="lazy">
                <div class="part-content">
                    <div class="part-meta">
                        <span class="badge"><?= h($part['brand']) ?></span>
                        <span><?= h($part['type']) ?></span>
                    </div>
                    <h2><?= h($part['name']) ?></h2>
                    <p class="muted"><?= h($part['description']) ?></p>
                    <div class="part-meta">
                        <span class="price"><?= money($part['price']) ?></span>
                        <span class="<?= (int) $part['stock'] <= 10 ? 'stock-low' : '' ?>">
                            Stock: <?= (int) $part['stock'] ?>
                        </span>
                    </div>
                </div>
            </article>
        <?php endforeach; ?>
    </section>
<?php else: ?>
    <div class="panel empty">Não existem peças que correspondam aos filtros.</div>
<?php endif; ?>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
