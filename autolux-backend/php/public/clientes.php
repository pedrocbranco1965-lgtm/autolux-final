<?php
/**
 * Clientes: listagem com pesquisa e formulário para criar novo cliente.
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Repositorios\ClienteRepository;

$titulo = 'Clientes';
$erros = [];

try {
    $repo = new ClienteRepository();

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $erros = $repo->validar($_POST);
        if (!$erros) {
            try {
                $id = $repo->criar($_POST);
                flash('sucesso', 'Cliente "' . trim($_POST['nome']) . '" criado com sucesso (nº ' . $id . ').');
                redirecionar('clientes.php');
            } catch (PDOException $e) {
                // 23000 = violação de chave única (NIF repetido)
                $erros['nif'] = $e->getCode() === '23000' ? 'Já existe um cliente com este NIF.' : 'Erro ao gravar: ' . $e->getMessage();
            }
        }
    }

    $pesquisa = trim($_GET['pesquisa'] ?? '');
    $lista = $repo->listar($pesquisa);
} catch (Throwable $excecao) {
    $titulo = 'Base de dados indisponível';
    require __DIR__ . '/../templates/erro.php';
    exit;
}

require __DIR__ . '/../templates/cabecalho.php';
?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Clientes</p>
    <h1>Base de clientes</h1>
    <p><?= count($lista) ?> cliente(s) registado(s) na BD1.</p>
  </div>
  <form class="pesquisa-simples" method="get" action="clientes.php">
    <input type="search" name="pesquisa" value="<?= e($pesquisa) ?>" placeholder="Nome, NIF ou email">
    <button class="botao botao-secundario" type="submit">Pesquisar</button>
  </form>
</section>

<div class="duas-colunas colunas-2-1">
  <section class="cartao">
    <table class="tabela">
      <thead><tr><th>Nome</th><th>NIF</th><th>Contacto</th><th class="num">Vendas</th><th class="num">Total gasto</th><th></th></tr></thead>
      <tbody>
        <?php if (!$lista): ?>
          <tr><td colspan="6" class="texto-suave">Nenhum cliente encontrado.</td></tr>
        <?php endif; ?>
        <?php foreach ($lista as $c): ?>
        <tr>
          <td><strong><?= e($c['nome']) ?></strong><br><small class="texto-suave"><?= e($c['morada']) ?></small></td>
          <td><?= e($c['nif']) ?></td>
          <td><?= e($c['email']) ?><br><small class="texto-suave"><?= e($c['telefone']) ?></small></td>
          <td class="num"><?= $c['num_vendas'] ?></td>
          <td class="num"><?= formatarPreco($c['total_gasto']) ?></td>
          <td><a class="botao botao-pequeno" href="venda.php?cliente_id=<?= $c['id'] ?>">Nova venda</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <section class="cartao">
    <h2>Novo cliente</h2>
    <form method="post" action="clientes.php" class="formulario" novalidate>
      <?= campoCsrf() ?>
      <label>Nome *
        <input type="text" name="nome" value="<?= antigo('nome') ?>" required>
        <?php if (isset($erros['nome'])): ?><span class="erro-campo"><?= e($erros['nome']) ?></span><?php endif; ?>
      </label>
      <label>NIF *
        <input type="text" name="nif" inputmode="numeric" maxlength="9" value="<?= antigo('nif') ?>" required>
        <?php if (isset($erros['nif'])): ?><span class="erro-campo"><?= e($erros['nif']) ?></span><?php endif; ?>
      </label>
      <label>Email *
        <input type="email" name="email" value="<?= antigo('email') ?>" required>
        <?php if (isset($erros['email'])): ?><span class="erro-campo"><?= e($erros['email']) ?></span><?php endif; ?>
      </label>
      <label>Telefone
        <input type="tel" name="telefone" value="<?= antigo('telefone') ?>">
      </label>
      <label>Morada
        <input type="text" name="morada" value="<?= antigo('morada') ?>">
      </label>
      <button class="botao" type="submit">Guardar cliente</button>
    </form>
  </section>
</div>
<?php require __DIR__ . '/../templates/rodape.php'; ?>
