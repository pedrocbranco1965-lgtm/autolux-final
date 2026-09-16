<?php
/**
 * Lista de encomendas a fornecedores (GET /api/encomendas) com filtro por
 * estado/fornecedor, detalhe de uma encomenda (?id=) e alteração de estado
 * (PATCH /api/encomendas/:id/estado). Tudo através da API Node.js.
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Api\ApiException;

$titulo = 'Encomendas a fornecedores';
$erroApi = null;
$estados = ['pendente', 'enviada', 'recebida', 'cancelada'];

// Alteração de estado (formulário POST na página de detalhe)
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['acao']) && $_POST['acao'] === 'estado') {
    try {
        $enc = $apiFornecedores->alterarEstado((int) $_POST['id'], $_POST['estado'] ?? '');
        flash('sucesso', 'Encomenda nº ' . $enc['id'] . ' passou ao estado "' . $enc['estado'] . '".');
    } catch (ApiException $e) {
        flash('erro', $e->getMessage());
    }
    redirecionar('encomendas_fornecedor.php?id=' . (int) $_POST['id']);
}

$id = (int) ($_GET['id'] ?? 0);
$filtroEstado = $_GET['estado'] ?? '';
$filtroFornecedor = $_GET['fornecedor_id'] ?? '';
$detalhe = null;
$lista = [];

try {
    if ($id > 0) {
        $detalhe = $apiFornecedores->obterEncomenda($id);
    } else {
        $lista = $apiFornecedores->listarEncomendas(['estado' => $filtroEstado, 'fornecedor_id' => $filtroFornecedor]);
    }
} catch (ApiException $e) {
    $erroApi = $e->getMessage();
}

require __DIR__ . '/../templates/cabecalho.php';
?>
<?php if ($erroApi): ?>
  <section class="cartao cartao-erro">
    <h1>API de fornecedores indisponível</h1>
    <p><?= e($erroApi) ?></p>
    <a class="botao botao-secundario" href="encomendas_fornecedor.php">Voltar à lista</a>
  </section>

<?php elseif ($detalhe): ?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Compras</p>
    <h1>Encomenda nº <?= $detalhe['id'] ?> <?= badgeEstado($detalhe['estado']) ?></h1>
    <p><?= e($detalhe['fornecedor_nome']) ?> · <?= formatarData($detalhe['data_encomenda']) ?><?= !empty($detalhe['criado_por']) ? ' · submetida por ' . e($detalhe['criado_por']) : '' ?></p>
  </div>
  <a class="botao botao-secundario" href="encomendas_fornecedor.php">Todas as encomendas</a>
</section>

<div class="duas-colunas colunas-2-1">
  <section class="cartao">
    <h2>Linhas</h2>
    <table class="tabela">
      <thead><tr><th>Referência</th><th>Descrição</th><th class="num">Qtd.</th><th class="num">Preço unit.</th><th class="num">Subtotal</th></tr></thead>
      <tbody>
        <?php foreach ($detalhe['itens'] as $i): ?>
        <tr>
          <td><code><?= e($i['referencia_peca']) ?></code></td>
          <td><?= e($i['descricao']) ?></td>
          <td class="num"><?= $i['quantidade'] ?></td>
          <td class="num"><?= formatarPreco($i['preco_unitario']) ?></td>
          <td class="num"><?= formatarPreco($i['subtotal']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot><tr><th colspan="4" class="num">Total</th><th class="num"><?= formatarPreco($detalhe['total']) ?></th></tr></tfoot>
    </table>
    <?php if ($detalhe['observacoes']): ?><p><em><?= e($detalhe['observacoes']) ?></em></p><?php endif; ?>
  </section>

  <aside class="cartao">
    <h2>Alterar estado</h2>
    <p class="texto-suave">Envia <code>PATCH /api/encomendas/<?= $detalhe['id'] ?>/estado</code>.</p>
    <form method="post" action="encomendas_fornecedor.php" class="formulario">
      <?= campoCsrf() ?>
      <input type="hidden" name="acao" value="estado">
      <input type="hidden" name="id" value="<?= $detalhe['id'] ?>">
      <label>Novo estado
        <select name="estado">
          <?php foreach ($estados as $est): ?>
            <option value="<?= $est ?>" <?= $est === $detalhe['estado'] ? 'selected' : '' ?>><?= ucfirst($est) ?></option>
          <?php endforeach; ?>
        </select>
      </label>
      <button class="botao botao-largo" type="submit">Guardar estado</button>
    </form>
  </aside>
</div>

<?php else: ?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Compras</p>
    <h1>Encomendas a fornecedores</h1>
    <p>Obtidas via <code>GET <?= e($config['API_BASE_URL']) ?>/encomendas</code> (Node.js → BD2).</p>
  </div>
  <a href="encomenda_fornecedor.php" class="botao">+ Nova encomenda</a>
</section>

<form class="filtros filtros-compactos" method="get" action="encomendas_fornecedor.php">
  <label>Estado
    <select name="estado">
      <option value="">Todos</option>
      <?php foreach ($estados as $est): ?>
        <option value="<?= $est ?>" <?= $est === $filtroEstado ? 'selected' : '' ?>><?= ucfirst($est) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <?php if ($filtroFornecedor !== ''): ?><input type="hidden" name="fornecedor_id" value="<?= e($filtroFornecedor) ?>"><?php endif; ?>
  <div class="filtros-acoes">
    <button class="botao" type="submit">Filtrar</button>
    <?php if ($filtroEstado !== '' || $filtroFornecedor !== ''): ?><a class="botao botao-secundario" href="encomendas_fornecedor.php">Limpar</a><?php endif; ?>
  </div>
</form>

<section class="cartao">
  <table class="tabela">
    <thead><tr><th>#</th><th>Data</th><th>Fornecedor</th><th>Submetida por</th><th>Estado</th><th class="num">Linhas</th><th class="num">Unidades</th><th class="num">Total</th><th></th></tr></thead>
    <tbody>
      <?php if (!$lista): ?>
        <tr><td colspan="9" class="texto-suave">Nenhuma encomenda encontrada.</td></tr>
      <?php endif; ?>
      <?php foreach ($lista as $enc): ?>
      <tr>
        <td>#<?= $enc['id'] ?></td>
        <td><?= formatarData($enc['data_encomenda']) ?></td>
        <td><?= e($enc['fornecedor_nome']) ?></td>
        <td><?= e($enc['criado_por'] ?? '—') ?></td>
        <td><?= badgeEstado($enc['estado']) ?></td>
        <td class="num"><?= $enc['num_linhas'] ?></td>
        <td class="num"><?= $enc['total_unidades'] ?></td>
        <td class="num"><strong><?= formatarPreco($enc['total']) ?></strong></td>
        <td><a class="botao botao-pequeno botao-secundario" href="encomendas_fornecedor.php?id=<?= $enc['id'] ?>">Detalhe</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>
<?php endif; ?>
<?php require __DIR__ . '/../templates/rodape.php'; ?>
