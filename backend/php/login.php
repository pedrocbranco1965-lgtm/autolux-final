<?php
require_once __DIR__ . '/includes/bootstrap.php';

$erro = '';
if (utilizador_atual()) {
    redirecionar('index.php');
}

if (metodo_post()) {
    if (!csrf_ok()) {
        $erro = 'Sessão inválida. Atualize a página e tente de novo.';
    } else {
        $user = trim((string) ($_POST['username'] ?? ''));
        $pass = (string) ($_POST['password'] ?? '');
        if (autenticar($user, $pass)) {
            redirecionar('index.php');
        }
        $erro = 'Utilizador ou palavra-passe incorretos.';
    }
}

layout_inicio('Entrar', 'login');
?>
<section class="login-wrap">
    <div class="card login-card">
        <p class="eyebrow">Backoffice de armazém</p>
        <h1>Entrar na AutoLux</h1>
        <p class="muted">Área reservada a funcionários para consultar stock, clientes, vendas e encomendas a fornecedores.</p>
        <?php if ($erro): ?>
            <div class="flash flash-erro"><?= e($erro) ?></div>
        <?php endif; ?>
        <form method="post" class="form">
            <input type="hidden" name="csrf" value="<?= e(csrf_token()) ?>">
            <label>
                Utilizador
                <input type="text" name="username" value="admin" required autocomplete="username">
            </label>
            <label>
                Palavra-passe
                <input type="password" name="password" value="autolux" required autocomplete="current-password">
            </label>
            <button class="btn btn-primary" type="submit">Entrar</button>
        </form>
        <p class="hint">Demo: <strong>admin</strong> / <strong>autolux</strong></p>
    </div>
</section>
<?php layout_fim(); ?>
