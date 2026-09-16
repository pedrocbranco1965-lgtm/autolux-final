<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

$pecasRepo = PecaFactory::criar();
$clientesRepo = new ClienteRepositorio(Database::vendas());
$vendaServico = new VendaServico(Database::vendas());
$pagamentos = CatalogoPagamentos::todos();

$clientes = $clientesRepo->todos();
$pecas = $pecasRepo->listar(null, null, null, null);
$pecaId = isset($_GET['peca_id']) ? (int) $_GET['peca_id'] : (int) ($_POST['peca_id'] ?? 0);
$pecaSelecionada = $pecaId ? $pecasRepo->obter($pecaId) : null;
$erro = '';

if (metodo_post()) {
    if (!csrf_ok()) {
        $erro = 'Sessão inválida. Atualize a página.';
    } else {
        $clienteId = (int) ($_POST['cliente_id'] ?? 0);
        $pecaId = (int) ($_POST['peca_id'] ?? 0);
        $quantidade = (int) ($_POST['quantidade'] ?? 0);
        $codigoPagamento = (string) ($_POST['tipo_pagamento'] ?? '');
        $metodo = CatalogoPagamentos::obter($codigoPagamento);
        $pecaSelecionada = $pecasRepo->obter($pecaId);

        if (!$clienteId || !$pecaId || !$metodo) {
            $erro = 'Selecione cliente, peça e tipo de pagamento.';
        } else {
            try {
                $id = $vendaServico->registar($clienteId, $pecaId, $quantidade, $metodo);
                flash('ok', 'Venda #' . $id . ' registada com ' . $metodo->nome() . '.');
                redirecionar('vendas.php');
            } catch (Throwable $ex) {
                $erro = $ex->getMessage();
            }
        }
    }
}

layout_inicio('Nova venda', 'venda');
?>
<section class="hero">
    <div>
        <p class="eyebrow">Requisito B</p>
        <h1>Encomenda / venda a cliente</h1>
        <p class="muted">Dropdown de clientes, informação da peça e tipo de pagamento extensível (basta acrescentar uma classe nova).</p>
    </div>
</section>

<?php if ($erro): ?>
    <div class="flash flash-erro"><?= e($erro) ?></div>
<?php endif; ?>

<form method="post" class="grid-2 venda-form">
    <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">

    <section class="card">
        <h2>1. Cliente</h2>
        <label>
            Cliente
            <select name="cliente_id" required>
                <option value="">Selecionar cliente…</option>
                <?php foreach ($clientes as $cliente): ?>
                    <option value="<?= (int) $cliente['id'] ?>" <?= (int) ($_POST['cliente_id'] ?? 0) === (int) $cliente['id'] ? 'selected' : '' ?>>
                        <?= e($cliente['nome']) ?> — NIF <?= e($cliente['nif']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <p class="hint">Os clientes vêm da Base de Dados 1 (MySQL), gerida pelo PHP.</p>

        <h2>2. Peça</h2>
        <label>
            Peça
            <select name="peca_id" id="peca_id" required>
                <option value="">Selecionar peça…</option>
                <?php foreach ($pecas as $peca): ?>
                    <option value="<?= (int) $peca['id'] ?>" <?= $pecaId === (int) $peca['id'] ? 'selected' : '' ?>>
                        <?= e($peca['marca']) ?> · <?= e($peca['nome']) ?> (<?= dinheiro($peca['preco']) ?>)
                    </option>
                <?php endforeach; ?>
            </select>
        </label>
        <label>
            Quantidade
            <input type="number" name="quantidade" min="1" value="<?= e((string) ($_POST['quantidade'] ?? '1')) ?>" required>
        </label>

        <?php if ($pecaSelecionada): ?>
            <div class="info-peca">
                <img src="<?= e($pecaSelecionada['imagem']) ?>" alt="">
                <div>
                    <p class="eyebrow"><?= e($pecaSelecionada['marca']) ?> · <?= e($pecaSelecionada['tipo']) ?></p>
                    <h3><?= e($pecaSelecionada['nome']) ?></h3>
                    <p>Ref. <?= e($pecaSelecionada['referencia']) ?></p>
                    <p><?= e($pecaSelecionada['descricao']) ?></p>
                    <p><strong><?= dinheiro($pecaSelecionada['preco']) ?></strong> · <?= (int) $pecaSelecionada['stock'] ?> un. em stock</p>
                </div>
            </div>
        <?php else: ?>
            <p class="muted">Escolha uma peça para ver os detalhes.</p>
        <?php endif; ?>
    </section>

    <section class="card">
        <h2>3. Tipo de pagamento</h2>
        <p class="muted">Os métodos são carregados a partir de <code>CatalogoPagamentos</code>. Novos métodos aparecem sozinhos nesta lista.</p>
        <div class="pagamentos">
            <?php foreach ($pagamentos as $i => $metodo): ?>
                <?php $selecionado = ($_POST['tipo_pagamento'] ?? 'numerario') === $metodo->codigo(); ?>
                <label class="pagamento">
                    <input type="radio" name="tipo_pagamento" value="<?= e($metodo->codigo()) ?>" <?= $selecionado ? 'checked' : '' ?> required>
                    <span>
                        <strong><?= e($metodo->nome()) ?></strong>
                        <small><?= e($metodo->descricao()) ?></small>
                    </span>
                </label>
            <?php endforeach; ?>
        </div>
        <button class="btn btn-primary" type="submit">Registar venda</button>
    </section>
</form>
<script>
document.getElementById('peca_id').addEventListener('change', function () {
    const params = new URLSearchParams(window.location.search);
    params.set('peca_id', this.value);
    window.location.search = params.toString();
});
</script>
<?php layout_fim(); ?>
