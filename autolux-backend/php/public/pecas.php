<?php
/**
 * A) Catálogo de peças
 * Lista as peças da BD1 com filtros por marca, tipo de peça e gama de preço
 * (mais pesquisa por texto e "só stock baixo"). Os filtros vêm por GET para
 * o URL poder ser partilhado/guardado.
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Repositorios\PecaRepository;

$titulo = 'Catálogo de peças';

$filtros = [
    'pesquisa'       => trim($_GET['pesquisa'] ?? ''),
    'marca_id'       => $_GET['marca_id'] ?? '',
    'tipo_id'        => $_GET['tipo_id'] ?? '',
    'preco_min'      => $_GET['preco_min'] ?? '',
    'preco_max'      => $_GET['preco_max'] ?? '',
    'so_stock_baixo' => $_GET['so_stock_baixo'] ?? '',
];
// Gamas de preço pré-definidas (dropdown) para além dos campos livres
$gamas = [
    ''        => 'Qualquer preço',
    '0-25'    => 'Até 25 €',
    '25-50'   => '25 € a 50 €',
    '50-100'  => '50 € a 100 €',
    '100-999999' => 'Mais de 100 €',
];
$gamaSelecionada = $_GET['gama'] ?? '';
if ($gamaSelecionada !== '' && isset($gamas[$gamaSelecionada])) {
    [$filtros['preco_min'], $filtros['preco_max']] = explode('-', $gamaSelecionada);
}
$haFiltros = array_filter($filtros, fn($v) => $v !== '');

try {
    $repo = new PecaRepository();
    $lista = $repo->listar($filtros);
    $marcas = $repo->marcas();
    $tipos = $repo->tiposPeca();
    $intervalo = $repo->intervaloPrecos();
} catch (Throwable $excecao) {
    $titulo = 'Base de dados indisponível';
    require __DIR__ . '/../templates/erro.php';
    exit;
}

require __DIR__ . '/../templates/cabecalho.php';
?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Catálogo</p>
    <h1>Peças em stock</h1>
    <p>Dados obtidos da base de dados <code><?= e($config['DB1_NAME']) ?></code> (tabela <code>pecas</code>).</p>
  </div>
</section>

<form class="filtros" method="get" action="pecas.php">
  <label>Pesquisa
    <input type="search" name="pesquisa" value="<?= e($filtros['pesquisa']) ?>" placeholder="Nome, referência ou descrição">
  </label>
  <label>Marca
    <select name="marca_id">
      <option value="">Todas</option>
      <?php foreach ($marcas as $m): ?>
        <option value="<?= $m['id'] ?>" <?= (string) $m['id'] === (string) $filtros['marca_id'] ? 'selected' : '' ?>><?= e($m['nome']) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Tipo de peça
    <select name="tipo_id">
      <option value="">Todos</option>
      <?php foreach ($tipos as $t): ?>
        <option value="<?= $t['id'] ?>" <?= (string) $t['id'] === (string) $filtros['tipo_id'] ? 'selected' : '' ?>><?= e($t['nome']) ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Gama de preço
    <select name="gama">
      <?php foreach ($gamas as $valor => $rotulo): ?>
        <option value="<?= $valor ?>" <?= $valor === $gamaSelecionada ? 'selected' : '' ?>><?= $rotulo ?></option>
      <?php endforeach; ?>
    </select>
  </label>
  <label>Preço mín. (€)
    <input type="number" step="0.01" min="0" name="preco_min" value="<?= e($_GET['preco_min'] ?? '') ?>" placeholder="<?= e($intervalo['minimo']) ?>">
  </label>
  <label>Preço máx. (€)
    <input type="number" step="0.01" min="0" name="preco_max" value="<?= e($_GET['preco_max'] ?? '') ?>" placeholder="<?= e($intervalo['maximo']) ?>">
  </label>
  <label class="caixa-verificacao">
    <input type="checkbox" name="so_stock_baixo" value="1" <?= $filtros['so_stock_baixo'] ? 'checked' : '' ?>> Só stock baixo
  </label>
  <div class="filtros-acoes">
    <button class="botao" type="submit">Filtrar</button>
    <?php if ($haFiltros): ?><a class="botao botao-secundario" href="pecas.php">Limpar</a><?php endif; ?>
  </div>
</form>

<p class="contagem"><?= count($lista) ?> peça(s) encontrada(s)</p>

<?php if (!$lista): ?>
  <section class="cartao vazio">
    <h2>Sem resultados</h2>
    <p>Altere ou limpe os filtros para ver mais peças.</p>
  </section>
<?php else: ?>
<div class="grelha-pecas">
  <?php foreach ($lista as $p): ?>
    <article class="peca <?= $p['stock'] <= $p['stock_minimo'] ? 'peca-stock-baixo' : '' ?>">
      <header>
        <span class="rotulo"><?= e($p['marca']) ?></span>
        <span class="etiqueta"><?= e($p['tipo']) ?></span>
      </header>
      <h3><?= e($p['nome']) ?></h3>
      <code class="referencia"><?= e($p['referencia']) ?></code>
      <p class="texto-suave"><?= e($p['descricao']) ?></p>
      <div class="peca-rodape">
        <strong class="preco"><?= formatarPreco($p['preco']) ?></strong>
        <span class="stock">Stock: <b><?= $p['stock'] ?></b><?= $p['stock'] <= $p['stock_minimo'] ? ' <em>(baixo)</em>' : '' ?></span>
      </div>
      <div class="peca-acoes">
        <?php if ($p['stock'] > 0): ?>
          <a class="botao botao-pequeno" href="venda.php?peca_id=<?= $p['id'] ?>">Vender</a>
        <?php else: ?>
          <span class="botao botao-pequeno botao-desativado">Esgotado</span>
        <?php endif; ?>
        <a class="botao botao-pequeno botao-secundario" href="encomenda_fornecedor.php?peca_id=<?= $p['id'] ?>">Encomendar ao fornecedor</a>
      </div>
    </article>
  <?php endforeach; ?>
</div>
<?php endif; ?>
<?php require __DIR__ . '/../templates/rodape.php'; ?>
