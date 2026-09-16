<?php
/**
 * Página de autenticação de funcionários.
 * É a única página pública (ver $paginasPublicas em src/bootstrap.php).
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Auth;

$titulo = 'Iniciar sessão';
$erro = null;

// Só aceita redirecionar para páginas internas (evita "open redirect" para sites externos)
$redirect = $_POST['redirect'] ?? $_GET['redirect'] ?? 'index.php';
if (!preg_match('/^[a-z_]+\.php(\?[^\s]*)?$/i', $redirect)) {
    $redirect = 'index.php';
}

if (Auth::autenticado()) {
    redirecionar($redirect);
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $erro = Auth::entrar($_POST['utilizador'] ?? '', $_POST['password'] ?? '');
        if ($erro === null) {
            flash('sucesso', 'Bem-vindo(a), ' . Auth::utilizador()['nome'] . '.');
            redirecionar($redirect);
        }
    } catch (Throwable $excecao) {
        $titulo = 'Base de dados indisponível';
        require __DIR__ . '/../templates/erro.php';
        exit;
    }
}
?>
<!doctype html>
<html lang="pt">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title><?= e($titulo) ?> | AutoLux Gestão</title>
  <link rel="stylesheet" href="assets/estilos.css">
</head>
<body class="pagina-login">
<main class="cartao-login">
  <a class="marca" href="login.php"><span class="marca-simbolo">AL</span> AutoLux <small>Armazém de peças</small></a>
  <p class="rotulo">Área reservada a funcionários</p>
  <h1>Iniciar sessão</h1>

  <?php foreach (obterFlash() as $f): ?>
    <div class="alerta alerta-<?= e($f['tipo']) ?>"><?= e($f['mensagem']) ?></div>
  <?php endforeach; ?>
  <?php if ($erro): ?><div class="alerta alerta-erro"><?= e($erro) ?></div><?php endif; ?>

  <form method="post" action="login.php" class="formulario" novalidate>
    <?= campoCsrf() ?>
    <input type="hidden" name="redirect" value="<?= e($redirect) ?>">
    <label>Utilizador
      <input type="text" name="utilizador" value="<?= antigo('utilizador') ?>" autocomplete="username" autofocus required>
    </label>
    <label>Password
      <input type="password" name="password" autocomplete="current-password" required>
    </label>
    <button class="botao botao-largo" type="submit">Entrar</button>
  </form>

  <details class="credenciais-teste">
    <summary>Credenciais de teste</summary>
    <ul>
      <li><code>admin</code> / <code>admin123</code> — administrador (gere funcionários)</li>
      <li><code>ana</code> / <code>ana123</code> — funcionária</li>
    </ul>
  </details>
</main>
</body>
</html>
