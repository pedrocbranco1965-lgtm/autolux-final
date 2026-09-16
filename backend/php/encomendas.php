<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

$api = new FornecedorApi(API_FORNECEDORES_URL);
$encomendas = [];
$erro = '';

if (!$api->saude()) {
    $erro = 'A API Node.js não está a responder em ' . API_FORNECEDORES_URL . '.';
} else {
    try {
        $encomendas = $api->encomendas();
    } catch (Throwable $ex) {
        $erro = $ex->getMessage();
    }
}

layout_inicio('Encomendas a fornecedores', 'encomendas');
?>
<section class="hero">
    <div>
        <p class="eyebrow">API Node.js · Base de Dados 2</p>
        <h1>Encomendas a fornecedores</h1>
        <p class="muted">Listagem obtida por GET /api/encomendas. Novas encomendas entram por POST a partir da página de fornecedores.</p>
    </div>
    <a class="btn btn-primary" href="fornecedores.php">Nova encomenda</a>
</section>

<?php if ($erro): ?>
    <div class="flash flash-erro"><?= e($erro) ?></div>
<?php elseif (!$encomendas): ?>
    <p class="muted">Ainda não há encomendas na API.</p>
<?php else: ?>
    <div class="table-wrap card">
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Data</th>
                    <th>Fornecedor</th>
                    <th>Peça</th>
                    <th>Qtd</th>
                    <th>Preço prev.</th>
                    <th>Estado</th>
                    <th>Observações</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($encomendas as $enc): ?>
                    <tr>
                        <td><?= (int) $enc['id'] ?></td>
                        <td><?= e($enc['criado_em']) ?></td>
                        <td><?= e($enc['fornecedor_nome']) ?></td>
                        <td><?= e($enc['peca_referencia']) ?> · <?= e($enc['peca_nome']) ?></td>
                        <td><?= (int) $enc['quantidade'] ?></td>
                        <td><?= dinheiro($enc['preco_previsto']) ?></td>
                        <td><span class="chip"><?= e($enc['estado']) ?></span></td>
                        <td><?= e($enc['observacoes'] ?? '') ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>
<?php layout_fim(); ?>
