<?php
/**
 * Gestão de funcionários (apenas administradores):
 * listar, criar, ativar/desativar e redefinir password.
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Auth;
use AutoLux\Repositorios\FuncionarioRepository;

Auth::exigirAdmin();

$titulo = 'Funcionários';
$erros = [];

try {
    $repo = new FuncionarioRepository();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $acao = $_POST['acao'] ?? 'criar';
        $id = (int) ($_POST['id'] ?? 0);

        if ($acao === 'criar') {
            $erros = $repo->validar($_POST);
            if (!$erros) {
                try {
                    $repo->criar($_POST);
                    flash('sucesso', 'Funcionário "' . trim($_POST['nome']) . '" criado. Já pode iniciar sessão com o utilizador "' . strtolower(trim($_POST['utilizador'])) . '".');
                    redirecionar('funcionarios.php');
                } catch (PDOException $e) {
                    $erros['utilizador'] = $e->getCode() === '23000' ? 'Já existe um funcionário com esse utilizador.' : $e->getMessage();
                }
            }
        } elseif ($acao === 'ativar' || $acao === 'desativar') {
            $alvo = $repo->obter($id);
            if (!$alvo) {
                flash('erro', 'Funcionário não encontrado.');
            } elseif ($acao === 'desativar' && $id === (int) $utilizadorAtual['id']) {
                flash('erro', 'Não pode desativar a sua própria conta.');
            } elseif ($acao === 'desativar' && $alvo['perfil'] === 'admin' && $repo->contarAdminsAtivos() <= 1) {
                flash('erro', 'Tem de existir pelo menos um administrador ativo.');
            } else {
                $repo->alterarAtivo($id, $acao === 'ativar');
                flash('sucesso', 'Conta de ' . $alvo['nome'] . ($acao === 'ativar' ? ' ativada.' : ' desativada.'));
            }
            redirecionar('funcionarios.php');
        } elseif ($acao === 'password') {
            $alvo = $repo->obter($id);
            $erroPw = FuncionarioRepository::validarPassword($_POST['password'] ?? '');
            if (!$alvo) {
                flash('erro', 'Funcionário não encontrado.');
            } elseif ($erroPw) {
                flash('erro', $erroPw);
            } else {
                $repo->alterarPassword($id, $_POST['password']);
                flash('sucesso', 'Password de ' . $alvo['nome'] . ' redefinida.');
            }
            redirecionar('funcionarios.php');
        }
    }

    $lista = $repo->listar();
} catch (Throwable $excecao) {
    $titulo = 'Base de dados indisponível';
    require __DIR__ . '/../templates/erro.php';
    exit;
}

require __DIR__ . '/../templates/cabecalho.php';
?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Administração</p>
    <h1>Funcionários</h1>
    <p><?= count($lista) ?> conta(s). As passwords são guardadas como hash bcrypt (<code>password_hash</code>).</p>
  </div>
</section>

<div class="duas-colunas colunas-2-1">
  <section class="cartao">
    <table class="tabela">
      <thead><tr><th>Nome</th><th>Utilizador</th><th>Perfil</th><th>Estado</th><th>Último login</th><th class="num">Vendas</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($lista as $f): ?>
        <tr class="<?= $f['ativo'] ? '' : 'linha-inativa' ?>">
          <td><strong><?= e($f['nome']) ?></strong><?= $f['id'] == $utilizadorAtual['id'] ? ' <span class="etiqueta">você</span>' : '' ?><br><small class="texto-suave"><?= e($f['email']) ?></small></td>
          <td><code><?= e($f['utilizador']) ?></code></td>
          <td><?= e(FuncionarioRepository::PERFIS[$f['perfil']]) ?></td>
          <td><?= $f['ativo'] ? '<span class="badge badge-sucesso">ativo</span>' : '<span class="badge badge-erro">inativo</span>' ?></td>
          <td><?= formatarData($f['ultimo_login']) ?></td>
          <td class="num"><?= $f['num_vendas'] ?></td>
          <td>
            <div class="acoes acoes-inline">
              <form method="post" action="funcionarios.php">
                <?= campoCsrf() ?>
                <input type="hidden" name="id" value="<?= $f['id'] ?>">
                <input type="hidden" name="acao" value="<?= $f['ativo'] ? 'desativar' : 'ativar' ?>">
                <button class="botao botao-pequeno botao-secundario" type="submit" <?= $f['id'] == $utilizadorAtual['id'] ? 'disabled title="Não pode desativar a sua conta"' : '' ?>><?= $f['ativo'] ? 'Desativar' : 'Ativar' ?></button>
              </form>
              <details class="popover">
                <summary class="botao botao-pequeno botao-secundario">Nova password</summary>
                <form method="post" action="funcionarios.php" class="formulario popover-conteudo">
                  <?= campoCsrf() ?>
                  <input type="hidden" name="id" value="<?= $f['id'] ?>">
                  <input type="hidden" name="acao" value="password">
                  <label>Nova password para <?= e($f['utilizador']) ?>
                    <input type="password" name="password" minlength="6" autocomplete="new-password" required>
                  </label>
                  <button class="botao botao-pequeno" type="submit">Guardar</button>
                </form>
              </details>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <section class="cartao">
    <h2>Novo funcionário</h2>
    <form method="post" action="funcionarios.php" class="formulario" novalidate>
      <?= campoCsrf() ?>
      <input type="hidden" name="acao" value="criar">
      <label>Nome *
        <input type="text" name="nome" value="<?= antigo('nome') ?>" required>
        <?php if (isset($erros['nome'])): ?><span class="erro-campo"><?= e($erros['nome']) ?></span><?php endif; ?>
      </label>
      <label>Utilizador *
        <input type="text" name="utilizador" value="<?= antigo('utilizador') ?>" autocomplete="off" required>
        <?php if (isset($erros['utilizador'])): ?><span class="erro-campo"><?= e($erros['utilizador']) ?></span><?php endif; ?>
      </label>
      <label>Email *
        <input type="email" name="email" value="<?= antigo('email') ?>" required>
        <?php if (isset($erros['email'])): ?><span class="erro-campo"><?= e($erros['email']) ?></span><?php endif; ?>
      </label>
      <label>Password * <small class="texto-suave">(mín. 6 caracteres)</small>
        <input type="password" name="password" autocomplete="new-password" required>
        <?php if (isset($erros['password'])): ?><span class="erro-campo"><?= e($erros['password']) ?></span><?php endif; ?>
      </label>
      <label>Perfil *
        <select name="perfil">
          <?php foreach (FuncionarioRepository::PERFIS as $codigo => $nome): ?>
            <option value="<?= $codigo ?>" <?= ($_POST['perfil'] ?? 'funcionario') === $codigo ? 'selected' : '' ?>><?= e($nome) ?></option>
          <?php endforeach; ?>
        </select>
        <?php if (isset($erros['perfil'])): ?><span class="erro-campo"><?= e($erros['perfil']) ?></span><?php endif; ?>
      </label>
      <button class="botao" type="submit">Criar funcionário</button>
    </form>
  </section>
</div>
<?php require __DIR__ . '/../templates/rodape.php'; ?>
