<?php
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/db.php';

$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $clienteId = (int) ($_POST['cliente_id'] ?? 0);
    $pecaId = (int) ($_POST['peca_id'] ?? 0);
    $pagamentoId = (int) ($_POST['metodo_pagamento_id'] ?? 0);
    $quantidade = (int) ($_POST['quantidade'] ?? 0);

    if ($clienteId <= 0 || $pecaId <= 0 || $pagamentoId <= 0 || $quantidade <= 0) {
        $error = 'Preencha todos os campos e indique uma quantidade valida.';
    } else {
        try {
            $pdo = db();
            $pdo->beginTransaction();

            $stmt = $pdo->prepare('SELECT id, nome, preco, stock FROM pecas WHERE id = :id FOR UPDATE');
            $stmt->execute(['id' => $pecaId]);
            $peca = $stmt->fetch();

            if (!$peca) {
                throw new RuntimeException('A peca escolhida nao existe.');
            }

            if ((int) $peca['stock'] < $quantidade) {
                throw new RuntimeException('Stock insuficiente para concluir a venda.');
            }

            $total = (float) $peca['preco'] * $quantidade;

            $stmt = $pdo->prepare(
                'INSERT INTO vendas (cliente_id, peca_id, metodo_pagamento_id, quantidade, total)
                 VALUES (:cliente_id, :peca_id, :metodo_pagamento_id, :quantidade, :total)'
            );
            $stmt->execute([
                'cliente_id' => $clienteId,
                'peca_id' => $pecaId,
                'metodo_pagamento_id' => $pagamentoId,
                'quantidade' => $quantidade,
                'total' => $total,
            ]);

            $stmt = $pdo->prepare('UPDATE pecas SET stock = stock - :quantidade WHERE id = :id');
            $stmt->execute(['quantidade' => $quantidade, 'id' => $pecaId]);

            $pdo->commit();
            $message = 'Venda registada com sucesso. Total: ' . money($total);
        } catch (Throwable $exception) {
            if (isset($pdo) && $pdo->inTransaction()) {
                $pdo->rollBack();
            }
            $error = $exception->getMessage();
        }
    }
}

$clientes = db()->query('SELECT id, nome FROM clientes ORDER BY nome')->fetchAll();
$pecas = db()->query('SELECT id, referencia, nome, preco, stock FROM pecas ORDER BY nome')->fetchAll();
$pagamentos = db()->query('SELECT id, nome FROM metodos_pagamento WHERE ativo = 1 ORDER BY nome')->fetchAll();
$selectedPecaId = (int) ($_GET['peca_id'] ?? ($_POST['peca_id'] ?? 0));

render_header('Venda a cliente');
?>

<h1>Formulario de encomenda/venda de peca para cliente</h1>
<p class="muted">
    Os clientes, pecas e metodos de pagamento sao carregados a partir da base de dados.
    Para adicionar pagamentos novos basta inserir um registo em <code>metodos_pagamento</code>.
</p>

<?php if ($message): ?>
    <div class="alert success"><?= e($message) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert error"><?= e($error) ?></div>
<?php endif; ?>

<form class="card form" method="post">
    <label>
        Cliente
        <select name="cliente_id" required>
            <option value="">Escolha um cliente</option>
            <?php foreach ($clientes as $cliente): ?>
                <option value="<?= e((string) $cliente['id']) ?>"><?= e($cliente['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Peca
        <select name="peca_id" required>
            <option value="">Escolha uma peca</option>
            <?php foreach ($pecas as $peca): ?>
                <option value="<?= e((string) $peca['id']) ?>" <?= $selectedPecaId === (int) $peca['id'] ? 'selected' : '' ?>>
                    <?= e($peca['referencia']) ?> - <?= e($peca['nome']) ?>
                    (<?= money($peca['preco']) ?>, stock <?= e((string) $peca['stock']) ?>)
                </option>
            <?php endforeach; ?>
        </select>
    </label>

    <label>
        Quantidade
        <input type="number" name="quantidade" min="1" value="1" required>
    </label>

    <label>
        Tipo de pagamento
        <select name="metodo_pagamento_id" required>
            <option value="">Escolha o metodo</option>
            <?php foreach ($pagamentos as $pagamento): ?>
                <option value="<?= e((string) $pagamento['id']) ?>"><?= e($pagamento['nome']) ?></option>
            <?php endforeach; ?>
        </select>
    </label>

    <button class="button" type="submit">Registar venda</button>
</form>

<section class="card">
    <h2>Ultimas vendas</h2>
    <?php
    $vendas = db()->query('
        SELECT v.criada_em, c.nome AS cliente, p.nome AS peca, mp.nome AS pagamento, v.quantidade, v.total
        FROM vendas v
        INNER JOIN clientes c ON c.id = v.cliente_id
        INNER JOIN pecas p ON p.id = v.peca_id
        INNER JOIN metodos_pagamento mp ON mp.id = v.metodo_pagamento_id
        ORDER BY v.criada_em DESC
        LIMIT 8
    ')->fetchAll();
    ?>

    <?php if (!$vendas): ?>
        <p class="muted">Ainda nao existem vendas registadas.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Cliente</th>
                    <th>Peca</th>
                    <th>Pagamento</th>
                    <th>Qtd.</th>
                    <th>Total</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($vendas as $venda): ?>
                    <tr>
                        <td><?= e($venda['criada_em']) ?></td>
                        <td><?= e($venda['cliente']) ?></td>
                        <td><?= e($venda['peca']) ?></td>
                        <td><?= e($venda['pagamento']) ?></td>
                        <td><?= e((string) $venda['quantidade']) ?></td>
                        <td><?= money($venda['total']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<?php render_footer(); ?>
