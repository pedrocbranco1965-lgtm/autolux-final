<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

$api = new FornecedorApi(API_FORNECEDORES_URL);
$pecasRepo = PecaFactory::criar();
$pecas = $pecasRepo->listar(null, null, null, null);
$erro = '';
$fornecedores = [];
$apiOk = $api->saude();

if ($apiOk) {
    try {
        $fornecedores = $api->fornecedores();
    } catch (Throwable $ex) {
        $apiOk = false;
        $erro = $ex->getMessage();
    }
}

if (metodo_post()) {
    if (!csrf_ok()) {
        $erro = 'Sessão inválida. Atualize a página.';
    } elseif (!$apiOk) {
        $erro = 'A API Node.js não está disponível.';
    } else {
        $pecaId = (int) ($_POST['peca_id'] ?? 0);
        $peca = $pecasRepo->obter($pecaId);
        if (!$peca) {
            $erro = 'Selecione uma peça do catálogo.';
        } else {
            try {
                $api->criarEncomenda([
                    'fornecedor_id' => (int) ($_POST['fornecedor_id'] ?? 0),
                    'peca_referencia' => $peca['referencia'],
                    'peca_nome' => $peca['nome'],
                    'quantidade' => (int) ($_POST['quantidade'] ?? 0),
                    'preco_previsto' => (float) ($_POST['preco_previsto'] ?? 0),
                    'observacoes' => trim((string) ($_POST['observacoes'] ?? '')),
                ]);
                flash('ok', 'Encomenda submetida na API Node.js.');
                redirecionar('encomendas.php');
            } catch (Throwable $ex) {
                $erro = $ex->getMessage();
            }
        }
    }
}

layout_inicio('Fornecedores', 'fornecedores');
?>
<section class="hero">
    <div>
        <p class="eyebrow">Requisito C · API Node.js</p>
        <h1>Fornecedores e nova encomenda</h1>
        <p class="muted">Os dados desta página não vêm do PHP/MySQL de vendas. São pedidos à web API em <?= e(API_FORNECEDORES_URL) ?>.</p>
    </div>
</section>

<?php if (!$apiOk): ?>
    <div class="flash flash-erro">
        Não foi possível contactar a API de fornecedores. Na pasta <code>backend</code> execute <code>npm install</code> e <code>npm start</code>.
        <?php if ($erro): ?><br><?= e($erro) ?><?php endif; ?>
    </div>
<?php else: ?>
    <?php if ($erro): ?>
        <div class="flash flash-erro"><?= e($erro) ?></div>
    <?php endif; ?>

    <div class="table-wrap card">
        <table>
            <thead>
                <tr>
                    <th>Fornecedor</th>
                    <th>Especialidade</th>
                    <th>Contacto</th>
                    <th>Prazo</th>
                    <th>Morada</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($fornecedores as $f): ?>
                    <tr>
                        <td>
                            <strong><?= e($f['nome']) ?></strong><br>
                            <span class="muted">NIF <?= e($f['nif']) ?></span>
                        </td>
                        <td><?= e($f['especialidade']) ?></td>
                        <td><?= e($f['email']) ?><br><?= e($f['telefone']) ?></td>
                        <td><?= (int) $f['prazo_entrega_dias'] ?> dias</td>
                        <td><?= e($f['morada']) ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <form method="post" class="card form-encomenda">
        <h2>Submeter encomenda ao fornecedor</h2>
        <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
        <div class="grid-form">
            <label>
                Fornecedor
                <select name="fornecedor_id" required>
                    <option value="">Selecionar…</option>
                    <?php foreach ($fornecedores as $f): ?>
                        <option value="<?= (int) $f['id'] ?>"><?= e($f['nome']) ?></option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Peça a repor
                <select name="peca_id" required>
                    <option value="">Selecionar…</option>
                    <?php foreach ($pecas as $peca): ?>
                        <option value="<?= (int) $peca['id'] ?>">
                            <?= e($peca['referencia']) ?> · <?= e($peca['nome']) ?> (stock <?= (int) $peca['stock'] ?>)
                        </option>
                    <?php endforeach; ?>
                </select>
            </label>
            <label>
                Quantidade
                <input type="number" name="quantidade" min="1" value="10" required>
            </label>
            <label>
                Preço previsto / un. (€)
                <input type="number" name="preco_previsto" min="0" step="0.01" value="0.00" required>
            </label>
        </div>
        <label>
            Observações
            <textarea name="observacoes" rows="3" placeholder="Ex.: reposição urgente, stock baixo no balcão."></textarea>
        </label>
        <button class="btn btn-primary" type="submit">Enviar encomenda via API</button>
    </form>
<?php endif; ?>
<?php layout_fim(); ?>
