<?php
declare(strict_types=1);

require_once __DIR__ . '/../src/functions.php';

$errors = [];
$suppliers = [];
$orders = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verifyCsrf();
    $payload = [
        'supplierId' => (int) ($_POST['supplier_id'] ?? 0),
        'items' => [[
            'supplierPartId' => (int) ($_POST['supplier_part_id'] ?? 0),
            'quantity' => (int) ($_POST['quantity'] ?? 0),
        ]],
        'notes' => trim((string) ($_POST['notes'] ?? '')),
    ];

    try {
        $result = apiRequest('/orders', 'POST', $payload);
        if ($result['status'] === 201) {
            flash(
                'success',
                'Encomenda #' . $result['data']['id'] . ' submetida à API. Total: ' .
                money($result['data']['total'])
            );
            redirect('/suppliers.php');
        }
        $errors[] = $result['data']['error'] ?? 'A API recusou a encomenda.';
        foreach ($result['data']['details'] ?? [] as $detail) {
            $errors[] = $detail;
        }
    } catch (Throwable $error) {
        $errors[] = 'A API Node.js não está disponível. Confirme se os serviços estão iniciados.';
    }
}

try {
    $suppliersResponse = apiRequest('/suppliers');
    $ordersResponse = apiRequest('/orders');
    if ($suppliersResponse['status'] === 200) {
        $suppliers = $suppliersResponse['data'];
    }
    if ($ordersResponse['status'] === 200) {
        $orders = $ordersResponse['data'];
    }
} catch (Throwable $error) {
    $errors[] = 'Não foi possível obter os dados da API Node.js.';
}

$pageTitle = 'Fornecedores';
$activePage = 'suppliers';
require __DIR__ . '/../src/partials/header.php';
?>
<section class="page-heading">
    <div>
        <h1>Fornecedores e encomendas</h1>
        <p class="subtitle">
            Dados recebidos através da <span class="badge api-label">API REST Node.js</span>
        </p>
    </div>
</section>

<?php if ($errors): ?>
    <div class="alert error">
        <ul><?php foreach (array_unique($errors) as $error): ?><li><?= h($error) ?></li><?php endforeach; ?></ul>
    </div>
<?php endif; ?>

<section class="two-columns">
    <article class="panel">
        <h2>Lista de fornecedores</h2>
        <?php if ($suppliers): ?>
            <?php foreach ($suppliers as $supplier): ?>
                <div>
                    <h3><?= h($supplier['name']) ?></h3>
                    <p class="muted">
                        <?= h($supplier['email']) ?> · <?= h($supplier['phone']) ?><br>
                        <?= h($supplier['address']) ?> · <?= count($supplier['parts']) ?> peça(s)
                    </p>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <p class="empty">Sem fornecedores disponíveis.</p>
        <?php endif; ?>
    </article>

    <form class="panel" method="post">
        <h2>Submeter encomenda</h2>
        <input type="hidden" name="csrf_token" value="<?= h(csrfToken()) ?>">
        <label>Fornecedor
            <select name="supplier_id" required>
                <option value="">Selecione</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <option value="<?= (int) $supplier['id'] ?>"><?= h($supplier['name']) ?></option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Peça
            <select name="supplier_part_id" required>
                <option value="">Selecione uma peça do fornecedor</option>
                <?php foreach ($suppliers as $supplier): ?>
                    <optgroup label="<?= h($supplier['name']) ?>">
                        <?php foreach ($supplier['parts'] as $part): ?>
                            <option value="<?= (int) $part['id'] ?>">
                                <?= h($part['sku']) ?> — <?= h($part['name']) ?> —
                                <?= money($part['unitCost']) ?> (disp.: <?= (int) $part['availableStock'] ?>)
                            </option>
                        <?php endforeach; ?>
                    </optgroup>
                <?php endforeach; ?>
            </select>
        </label>
        <label>Quantidade
            <input type="number" name="quantity" min="1" max="999" value="1" required>
        </label>
        <label>Observações
            <textarea name="notes" maxlength="500" placeholder="Opcional"></textarea>
        </label>
        <button class="button gold" type="submit" <?= $suppliers ? '' : 'disabled' ?>>Enviar à API</button>
    </form>
</section>

<section class="panel table-wrap">
    <h2>Encomendas já submetidas</h2>
    <?php if ($orders): ?>
        <table>
            <thead><tr><th>N.º</th><th>Data</th><th>Fornecedor</th><th>Estado</th><th>Total</th></tr></thead>
            <tbody>
            <?php foreach ($orders as $order): ?>
                <tr>
                    <td>#<?= (int) $order['id'] ?></td>
                    <td><?= h(date('d/m/Y H:i', strtotime($order['createdAt']))) ?></td>
                    <td><?= h($order['supplierName']) ?></td>
                    <td><span class="badge"><?= h($order['status']) ?></span></td>
                    <td><?= money($order['total']) ?></td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p class="empty">Ainda não existem encomendas.</p>
    <?php endif; ?>
</section>
<?php require __DIR__ . '/../src/partials/footer.php'; ?>
