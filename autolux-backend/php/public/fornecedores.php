<?php
/**
 * C) Lista de fornecedores
 * Os dados NÃO vêm da BD1: são pedidos por HTTP à Web API Node.js
 * (GET /api/fornecedores), que por sua vez lê a BD2. Também permite criar
 * um fornecedor (POST /api/fornecedores).
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Api\ApiException;

$titulo = 'Fornecedores';
$erroApi = null;
$erros = [];
$lista = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        $novo = $apiFornecedores->criarFornecedor([
            'nome'               => trim($_POST['nome'] ?? ''),
            'nif'                => trim($_POST['nif'] ?? ''),
            'email'              => trim($_POST['email'] ?? ''),
            'telefone'           => trim($_POST['telefone'] ?? ''),
            'morada'             => trim($_POST['morada'] ?? ''),
            'prazo_entrega_dias' => (int) ($_POST['prazo_entrega_dias'] ?? 5),
        ]);
        flash('sucesso', 'Fornecedor "' . $novo['nome'] . '" criado através da API Node.js (id ' . $novo['id'] . ').');
        redirecionar('fornecedores.php');
    } catch (ApiException $e) {
        $erros['geral'] = $e->getMessage(); // a API devolve as mensagens de validação
    }
}

try {
    $lista = $apiFornecedores->listarFornecedores(isset($_GET['todos']));
} catch (ApiException $e) {
    $erroApi = $e->getMessage();
}

require __DIR__ . '/../templates/cabecalho.php';
?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Compras</p>
    <h1>Fornecedores</h1>
    <p>Obtidos via <code>GET <?= e($config['API_BASE_URL']) ?>/fornecedores</code> (Node.js → BD2).</p>
  </div>
  <a href="encomenda_fornecedor.php" class="botao">+ Nova encomenda</a>
</section>

<?php if ($erroApi): ?>
  <section class="cartao cartao-erro">
    <h2>API de fornecedores indisponível</h2>
    <p><?= e($erroApi) ?></p>
    <p class="texto-suave">Arranque a API com <code>npm run start:api</code> (ou <code>npm start</code> para arrancar tudo) e recarregue a página.</p>
  </section>
<?php else: ?>
<div class="duas-colunas colunas-2-1">
  <section class="cartao">
    <div class="cartao-topo">
      <h2><?= count($lista) ?> fornecedor(es)</h2>
      <a href="fornecedores.php<?= isset($_GET['todos']) ? '' : '?todos=1' ?>"><?= isset($_GET['todos']) ? 'Só ativos' : 'Incluir inativos' ?></a>
    </div>
    <table class="tabela">
      <thead><tr><th>Nome</th><th>NIF</th><th>Contacto</th><th class="num">Prazo</th><th class="num">Encomendas</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($lista as $f): ?>
        <tr class="<?= $f['ativo'] ? '' : 'linha-inativa' ?>">
          <td><strong><?= e($f['nome']) ?></strong><br><small class="texto-suave"><?= e($f['morada']) ?></small></td>
          <td><?= e($f['nif']) ?></td>
          <td><?= e($f['email']) ?><br><small class="texto-suave"><?= e($f['telefone']) ?></small></td>
          <td class="num"><?= $f['prazo_entrega_dias'] ?> dias</td>
          <td class="num"><a href="encomendas_fornecedor.php?fornecedor_id=<?= $f['id'] ?>"><?= $f['total_encomendas'] ?></a></td>
          <td><a class="botao botao-pequeno" href="encomenda_fornecedor.php?fornecedor_id=<?= $f['id'] ?>">Encomendar</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  </section>

  <section class="cartao">
    <h2>Novo fornecedor</h2>
    <?php if (isset($erros['geral'])): ?><div class="alerta alerta-erro"><?= e($erros['geral']) ?></div><?php endif; ?>
    <form method="post" action="fornecedores.php" class="formulario" novalidate>
      <?= campoCsrf() ?>
      <label>Nome *<input type="text" name="nome" value="<?= antigo('nome') ?>" required></label>
      <label>NIF *<input type="text" name="nif" maxlength="9" inputmode="numeric" value="<?= antigo('nif') ?>" required></label>
      <label>Email *<input type="email" name="email" value="<?= antigo('email') ?>" required></label>
      <label>Telefone<input type="tel" name="telefone" value="<?= antigo('telefone') ?>"></label>
      <label>Morada<input type="text" name="morada" value="<?= antigo('morada') ?>"></label>
      <label>Prazo de entrega (dias)<input type="number" name="prazo_entrega_dias" min="1" value="<?= antigo('prazo_entrega_dias', 5) ?>"></label>
      <button class="botao" type="submit">Guardar via API</button>
    </form>
  </section>
</div>
<?php endif; ?>
<?php require __DIR__ . '/../templates/rodape.php'; ?>
