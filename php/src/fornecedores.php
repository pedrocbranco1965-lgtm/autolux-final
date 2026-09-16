<?php
require_once __DIR__ . '/layout.php';
require_once __DIR__ . '/db.php';

$config = require __DIR__ . '/config.php';
$apiUrl = $config['node_api_url'];
$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $payload = [
        'fornecedor_id' => (int) ($_POST['fornecedor_id'] ?? 0),
        'referencia_peca' => trim($_POST['referencia_peca'] ?? ''),
        'nome_peca' => trim($_POST['nome_peca'] ?? ''),
        'quantidade' => (int) ($_POST['quantidade'] ?? 0),
        'observacoes' => trim($_POST['observacoes'] ?? ''),
    ];

    $result = http_json($apiUrl . '/api/encomendas', 'POST', $payload);
    if ($result['ok']) {
        $message = 'Encomenda enviada para a API Node.js com sucesso.';
    } else {
        $error = $result['error'];
    }
}

$fornecedoresResult = http_json($apiUrl . '/api/fornecedores');
$encomendasResult = http_json($apiUrl . '/api/encomendas');
$fornecedores = $fornecedoresResult['ok'] ? $fornecedoresResult['data'] : [];
$encomendas = $encomendasResult['ok'] ? $encomendasResult['data'] : [];
$pecas = db()->query('SELECT referencia, nome FROM pecas ORDER BY nome')->fetchAll();

if (!$fornecedoresResult['ok']) {
    $error = $fornecedoresResult['error'];
}

render_header('Fornecedores');
?>

<h1>Lista de fornecedores e submissao de encomenda</h1>
<p class="muted">
    Esta pagina PHP obtem dados e submete encomendas atraves da API Node.js em
    <code><?= e($apiUrl) ?></code>.
</p>

<?php if ($message): ?>
    <div class="alert success"><?= e($message) ?></div>
<?php endif; ?>

<?php if ($error): ?>
    <div class="alert error"><?= e($error) ?></div>
<?php endif; ?>

<section class="grid two-columns">
    <article class="card">
        <h2>Fornecedores</h2>
        <?php if (!$fornecedores): ?>
            <p class="muted">Nao foi possivel carregar fornecedores.</p>
        <?php else: ?>
            <div class="supplier-list">
                <?php foreach ($fornecedores as $fornecedor): ?>
                    <div>
                        <strong><?= e($fornecedor['nome']) ?></strong>
                        <span><?= e($fornecedor['especialidade']) ?></span>
                        <small><?= e($fornecedor['email']) ?> | <?= e($fornecedor['telefone']) ?></small>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php endif; ?>
    </article>

    <article class="card">
        <h2>Nova encomenda a fornecedor</h2>
        <form class="form" method="post">
            <label>
                Fornecedor
                <select name="fornecedor_id" required>
                    <option value="">Escolha um fornecedor</option>
                    <?php foreach ($fornecedores as $fornecedor): ?>
                        <option value="<?= e((string) $fornecedor['id']) ?>"><?= e($fornecedor['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Peca
                <select name="referencia_nome" onchange="preencherPeca(this)">
                    <option value="">Pode escolher uma peca existente</option>
                    <?php foreach ($pecas as $peca): ?>
                        <option value="<?= e($peca['referencia'] . '|' . $peca['nome']) ?>">
                            <?= e($peca['referencia']) ?> - <?= e($peca['nome']) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>

            <label>
                Referencia da peca
                <input id="referencia_peca" name="referencia_peca" required>
            </label>

            <label>
                Nome da peca
                <input id="nome_peca" name="nome_peca" required>
            </label>

            <label>
                Quantidade
                <input type="number" name="quantidade" min="1" value="5" required>
            </label>

            <label>
                Observacoes
                <textarea name="observacoes" rows="3" placeholder="Ex: urgente, fornecedor alternativo..."></textarea>
            </label>

            <button class="button" type="submit">Submeter encomenda</button>
        </form>
    </article>
</section>

<section class="card">
    <h2>Encomendas a fornecedores</h2>
    <?php if (!$encomendas): ?>
        <p class="muted">Ainda nao existem encomendas carregadas pela API.</p>
    <?php else: ?>
        <table>
            <thead>
                <tr>
                    <th>Data</th>
                    <th>Fornecedor</th>
                    <th>Peca</th>
                    <th>Referencia</th>
                    <th>Qtd.</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($encomendas as $encomenda): ?>
                    <tr>
                        <td><?= e($encomenda['criada_em']) ?></td>
                        <td><?= e($encomenda['fornecedor']) ?></td>
                        <td><?= e($encomenda['nome_peca']) ?></td>
                        <td><?= e($encomenda['referencia_peca']) ?></td>
                        <td><?= e((string) $encomenda['quantidade']) ?></td>
                        <td><span class="badge"><?= e($encomenda['estado']) ?></span></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    <?php endif; ?>
</section>

<script>
function preencherPeca(select) {
    if (!select.value) {
        return;
    }

    const [referencia, nome] = select.value.split('|');
    document.getElementById('referencia_peca').value = referencia;
    document.getElementById('nome_peca').value = nome;
}
</script>

<?php render_footer(); ?>
