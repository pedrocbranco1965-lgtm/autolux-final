<?php
/**
 * C) Submissão de encomenda a fornecedor
 * A lista de fornecedores vem da API Node.js (GET /api/fornecedores) e a
 * encomenda é submetida à mesma API (POST /api/encomendas), que a grava na BD2.
 * As peças a encomendar são escolhidas do catálogo da BD1 (para sugerir
 * referência, descrição e um preço de custo), mas podem ser editadas à mão.
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Api\ApiException;
use AutoLux\Repositorios\PecaRepository;

$titulo = 'Nova encomenda a fornecedor';
$erros = [];
$erroApi = null;

try {
    $pecas = (new PecaRepository())->listar();
} catch (Throwable $excecao) {
    $titulo = 'Base de dados indisponível';
    require __DIR__ . '/../templates/erro.php';
    exit;
}

try {
    $fornecedores = $apiFornecedores->listarFornecedores();
} catch (ApiException $e) {
    $fornecedores = [];
    $erroApi = $e->getMessage();
}

$fornecedorSelecionado = (int) ($_POST['fornecedor_id'] ?? $_GET['fornecedor_id'] ?? 0);
$observacoes = trim($_POST['observacoes'] ?? '');

// Linhas da encomenda (arrays paralelos vindos do formulário)
$linhas = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $n = count((array) ($_POST['referencia'] ?? []));
    for ($i = 0; $i < $n; $i++) {
        $linhas[] = [
            'referencia'     => trim($_POST['referencia'][$i] ?? ''),
            'descricao'      => trim($_POST['descricao'][$i] ?? ''),
            'quantidade'     => (int) ($_POST['quantidade'][$i] ?? 0),
            'preco_unitario' => str_replace(',', '.', trim($_POST['preco_unitario'][$i] ?? '')),
        ];
    }
} else {
    // Pré-preenche a partir de uma peça do catálogo (?peca_id=) — sugere repor até 2x o stock mínimo
    $pecaInicial = null;
    if (!empty($_GET['peca_id'])) {
        foreach ($pecas as $p) {
            if ((int) $p['id'] === (int) $_GET['peca_id']) {
                $pecaInicial = $p;
            }
        }
    }
    $linhas[] = $pecaInicial ? [
        'referencia'     => $pecaInicial['referencia'],
        'descricao'      => $pecaInicial['marca'] . ' ' . $pecaInicial['nome'],
        'quantidade'     => max(1, (int) $pecaInicial['stock_minimo'] * 2 - (int) $pecaInicial['stock']),
        'preco_unitario' => number_format((float) $pecaInicial['preco'] * 0.65, 2, '.', ''), // preço de custo estimado
    ] : ['referencia' => '', 'descricao' => '', 'quantidade' => 1, 'preco_unitario' => ''];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && !$erroApi) {
    if ($fornecedorSelecionado <= 0) {
        $erros['fornecedor_id'] = 'Selecione o fornecedor.';
    }
    $itens = [];
    foreach ($linhas as $i => $l) {
        if ($l['referencia'] === '' && $l['descricao'] === '' && $l['quantidade'] <= 0) {
            continue;
        }
        if ($l['referencia'] === '' || $l['descricao'] === '') {
            $erros["linha_$i"] = 'Referência e descrição são obrigatórias.';
        } elseif ($l['quantidade'] <= 0) {
            $erros["linha_$i"] = 'Quantidade deve ser pelo menos 1.';
        } elseif (!is_numeric($l['preco_unitario']) || (float) $l['preco_unitario'] < 0) {
            $erros["linha_$i"] = 'Preço unitário inválido.';
        } else {
            $itens[] = [
                'referencia_peca' => $l['referencia'],
                'descricao'       => $l['descricao'],
                'quantidade'      => $l['quantidade'],
                'preco_unitario'  => (float) $l['preco_unitario'],
            ];
        }
    }
    if (!$itens && !$erros) {
        $erros['itens'] = 'Adicione pelo menos uma linha à encomenda.';
    }

    if (!$erros) {
        try {
            $encomenda = $apiFornecedores->submeterEncomenda($fornecedorSelecionado, $itens, $observacoes ?: null);
            flash('sucesso', 'Encomenda nº ' . $encomenda['id'] . ' submetida ao fornecedor ' . $encomenda['fornecedor_nome'] .
                ' através da API Node.js. Total: ' . formatarPreco($encomenda['total']) . '.');
            redirecionar('encomendas_fornecedor.php?id=' . $encomenda['id']);
        } catch (ApiException $e) {
            $erros['itens'] = 'A API rejeitou a encomenda: ' . $e->getMessage();
        }
    }
}

$pecasJson = json_encode(array_map(fn($p) => [
    'id' => (int) $p['id'], 'referencia' => $p['referencia'], 'descricao' => $p['marca'] . ' ' . $p['nome'],
    'preco_custo' => round((float) $p['preco'] * 0.65, 2), 'stock' => (int) $p['stock'], 'stock_minimo' => (int) $p['stock_minimo'],
], $pecas), JSON_UNESCAPED_UNICODE);

require __DIR__ . '/../templates/cabecalho.php';
?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Compras</p>
    <h1>Encomenda a fornecedor</h1>
    <p>Submetida via <code>POST <?= e($config['API_BASE_URL']) ?>/encomendas</code> (Node.js → BD2).</p>
  </div>
</section>

<?php if ($erroApi): ?>
  <section class="cartao cartao-erro">
    <h2>API de fornecedores indisponível</h2>
    <p><?= e($erroApi) ?></p>
    <p class="texto-suave">Arranque a API com <code>npm run start:api</code> e recarregue a página.</p>
  </section>
<?php else: ?>
<?php if (isset($erros['itens'])): ?><div class="alerta alerta-erro"><?= e($erros['itens']) ?></div><?php endif; ?>

<form method="post" action="encomenda_fornecedor.php" class="formulario" id="form-encomenda" novalidate>
  <div class="duas-colunas colunas-2-1">
    <div>
      <section class="cartao">
        <h2>1. Fornecedor</h2>
        <label>Fornecedor *
          <select name="fornecedor_id" required>
            <option value="">— Selecione o fornecedor —</option>
            <?php foreach ($fornecedores as $f): ?>
              <option value="<?= $f['id'] ?>" <?= $f['id'] == $fornecedorSelecionado ? 'selected' : '' ?>><?= e($f['nome']) ?> · entrega em <?= $f['prazo_entrega_dias'] ?> dias</option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($erros['fornecedor_id'])): ?><span class="erro-campo"><?= e($erros['fornecedor_id']) ?></span><?php endif; ?>
        </label>
      </section>

      <section class="cartao">
        <div class="cartao-topo">
          <h2>2. Peças a encomendar</h2>
          <div class="acoes">
            <select id="seletor-catalogo" class="select-compacto">
              <option value="">Adicionar do catálogo…</option>
              <?php foreach ($pecas as $p): ?>
                <option value="<?= $p['id'] ?>"><?= e($p['referencia']) ?> · <?= e($p['nome']) ?> (stock <?= $p['stock'] ?>/<?= $p['stock_minimo'] ?>)</option>
              <?php endforeach; ?>
            </select>
            <button type="button" class="botao botao-pequeno botao-secundario" id="adicionar-linha-vazia">+ Linha manual</button>
          </div>
        </div>

        <div class="tabela-linhas">
          <div class="tabela-linhas-cabecalho"><span>Referência</span><span>Descrição</span><span>Qtd.</span><span>Preço unit. (€)</span><span></span></div>
          <div id="linhas-encomenda">
            <?php foreach ($linhas as $i => $l): ?>
            <div class="linha-encomenda">
              <input type="text" name="referencia[]" value="<?= e($l['referencia']) ?>" placeholder="REF-000" required>
              <input type="text" name="descricao[]" value="<?= e($l['descricao']) ?>" placeholder="Descrição da peça" required>
              <input type="number" name="quantidade[]" class="input-quantidade" min="1" value="<?= $l['quantidade'] ?: 1 ?>" required>
              <input type="number" name="preco_unitario[]" class="input-preco" min="0" step="0.01" value="<?= e($l['preco_unitario']) ?>" placeholder="0.00" required>
              <button type="button" class="botao-icone remover-linha" aria-label="Remover linha">×</button>
              <?php if (isset($erros["linha_$i"])): ?><span class="erro-campo linha-erro"><?= e($erros["linha_$i"]) ?></span><?php endif; ?>
            </div>
            <?php endforeach; ?>
          </div>
        </div>

        <template id="template-linha-encomenda">
          <div class="linha-encomenda">
            <input type="text" name="referencia[]" placeholder="REF-000" required>
            <input type="text" name="descricao[]" placeholder="Descrição da peça" required>
            <input type="number" name="quantidade[]" class="input-quantidade" min="1" value="1" required>
            <input type="number" name="preco_unitario[]" class="input-preco" min="0" step="0.01" placeholder="0.00" required>
            <button type="button" class="botao-icone remover-linha" aria-label="Remover linha">×</button>
          </div>
        </template>
      </section>
    </div>

    <aside class="cartao resumo-venda">
      <h2>Resumo</h2>
      <div class="total-linha"><span>Total estimado</span><strong id="resumo-total">0,00 €</strong></div>
      <label>Observações
        <textarea name="observacoes" rows="3" maxlength="255" placeholder="Ex.: urgente, entregar de manhã"><?= e($observacoes) ?></textarea>
      </label>
      <button class="botao botao-largo" type="submit">Submeter encomenda</button>
      <a class="botao botao-secundario botao-largo" href="encomendas_fornecedor.php">Ver encomendas</a>
    </aside>
  </div>
</form>

<script id="dados-pecas" type="application/json"><?= $pecasJson ?></script>
<?php endif; ?>
<?php require __DIR__ . '/../templates/rodape.php'; ?>
