<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/functions.php';

$db = salesDb();
$errors = [];
$selectedClient = (int) ($_POST['client_id'] ?? 0);
$selectedPart = (int) ($_POST['part_id'] ?? ($_GET['part_id'] ?? 0));
$selectedPayment = (int) ($_POST['payment_method_id'] ?? 0);
$quantity = max(1, (int) ($_POST['quantity'] ?? 1));

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    try {
        $db->beginTransaction();

        $clientStatement = $db->prepare('SELECT id FROM clients WHERE id = ?');
        $clientStatement->execute([$selectedClient]);
        $paymentStatement = $db->prepare('SELECT id FROM payment_methods WHERE id = ? AND active = TRUE');
        $paymentStatement->execute([$selectedPayment]);
        $partStatement = $db->prepare('SELECT id, name, price, stock FROM parts WHERE id = ? FOR UPDATE');
        $partStatement->execute([$selectedPart]);
        $part = $partStatement->fetch();

        if (!$clientStatement->fetch()) {
            $errors[] = 'Selecione um cliente válido.';
        }
        if (!$paymentStatement->fetch()) {
            $errors[] = 'Selecione um método de pagamento válido.';
        }
        if (!$part) {
            $errors[] = 'Selecione uma peça válida.';
        }
        if ($quantity < 1 || $quantity > 999) {
            $errors[] = 'A quantidade deve estar entre 1 e 999.';
        } elseif ($part && $quantity > (int) $part['stock']) {
            $errors[] = "Stock insuficiente. Existem apenas {$part['stock']} unidade(s).";
        }

        if ($errors) {
            $db->rollBack();
        } else {
            $total = round((float) $part['price'] * $quantity, 2);
            $saleStatement = $db->prepare(
                'INSERT INTO sales (client_id, payment_method_id, total) VALUES (?, ?, ?)'
            );
            $saleStatement->execute([$selectedClient, $selectedPayment, $total]);
            $saleId = (int) $db->lastInsertId();

            $itemStatement = $db->prepare(
                'INSERT INTO sale_items (sale_id, part_id, quantity, unit_price) VALUES (?, ?, ?, ?)'
            );
            $itemStatement->execute([$saleId, $selectedPart, $quantity, $part['price']]);
            $stockStatement = $db->prepare('UPDATE parts SET stock = stock - ? WHERE id = ?');
            $stockStatement->execute([$quantity, $selectedPart]);
            $db->commit();

            flash('success', "Venda #{$saleId} registada com sucesso ({$quantity} × {$part['name']}).");
            redirect('/sales.php');
        }
    } catch (Throwable $error) {
        if ($db->inTransaction()) {
            $db->rollBack();
        }
        $errors[] = 'Não foi possível registar a venda. Tente novamente.';
    }
}

$clients = $db->query('SELECT id, name, email FROM clients ORDER BY name')->fetchAll();
$parts = $db->query('SELECT id, sku, name, price, stock FROM parts WHERE stock > 0 ORDER BY name')->fetchAll();
$paymentMethods = $db->query(
    'SELECT id, name FROM payment_methods WHERE active = TRUE ORDER BY id'
)->fetchAll();

$pageTitle = 'Nova venda';
$activePage = 'sale';
require __DIR__ . '/../src/partials/header.php';
?>
<section class="page-heading">
    <div>
        <h1>Encomenda de peça para cliente</h1>
        <p class="subtitle">Registe a venda e atualize o stock automaticamente.</p>
    </div>
</section>

<?php if ($errors): ?>
    <div class="alert error">
        <strong>Corrija os seguintes dados:</strong>
        <ul>
            <?php foreach ($errors as $error): ?><li><?= h($error) ?></li><?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<form class="panel" method="post">
    <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
    <div class="form-grid">
        <label>Cliente
            <select name="client_id" required>
                <option value="">Selecione um cliente</option>
                <?php foreach ($clients as $client): ?>
                    <option value="<?= (int) $client['id'] ?>"
                        <?= $selectedClient === (int) $client['id'] ? 'selected' : '' ?>>
                        <?= h($client['name']) ?> — <?= h($client['email']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Método de pagamento
            <select name="payment_method_id" required>
                <option value="">Selecione o pagamento</option>
                <?php foreach ($paymentMethods as $method): ?>
                    <option value="<?= (int) $method['id'] ?>"
                        <?= $selectedPayment === (int) $method['id'] ? 'selected' : '' ?>>
                        <?= h($method['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label class="full">Peça
            <select name="part_id" required>
                <option value="">Selecione uma peça</option>
                <?php foreach ($parts as $part): ?>
                    <option value="<?= (int) $part['id'] ?>"
                        <?= $selectedPart === (int) $part['id'] ? 'selected' : '' ?>>
                        <?= h($part['sku']) ?> — <?= h($part['name']) ?> —
                        <?= money($part['price']) ?> (stock: <?= (int) $part['stock'] ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Quantidade
            <input type="number" name="quantity" min="1" max="999" value="<?= $quantity ?>" required>
        </label>
    </div>
    <p class="muted">O preço é obtido da base de dados e confirmado no servidor.</p>
    <button class="button gold" type="submit">Confirmar venda</button>
</form>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
