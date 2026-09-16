<?php
/**
 * B) Formulário de encomenda/venda de peças a um cliente
 *  - dropdown de clientes (BD1)
 *  - uma ou mais linhas de peça com informação (referência, preço, stock)
 *  - tipo de pagamento escolhido entre os métodos registados em
 *    php/config/pagamentos.php (extensível sem tocar neste ficheiro)
 * Ao submeter, a venda é gravada numa transação e o stock é abatido.
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Repositorios\ClienteRepository;
use AutoLux\Repositorios\PecaRepository;
use AutoLux\Repositorios\VendaRepository;

$titulo = 'Nova venda';
$erros = [];

try {
    $clientes = (new ClienteRepository())->paraDropdown();
    $pecasRepo = new PecaRepository();
    $pecas = $pecasRepo->listar();
} catch (Throwable $excecao) {
    $titulo = 'Base de dados indisponível';
    require __DIR__ . '/../templates/erro.php';
    exit;
}

// Valores iniciais: podem vir por GET a partir do catálogo (?peca_id=) ou dos clientes (?cliente_id=)
$clienteSelecionado = (int) ($_POST['cliente_id'] ?? $_GET['cliente_id'] ?? 0);
$linhas = [];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $idsPeca = $_POST['peca_id'] ?? [];
    $quantidades = $_POST['quantidade'] ?? [];
    foreach ((array) $idsPeca as $i => $pecaId) {
        $linhas[] = ['peca_id' => (int) $pecaId, 'quantidade' => (int) ($quantidades[$i] ?? 0)];
    }
} else {
    $linhas[] = ['peca_id' => (int) ($_GET['peca_id'] ?? 0), 'quantidade' => 1];
}
$metodoSelecionado = $_POST['tipo_pagamento'] ?? '';
$campoExtra = trim($_POST['campo_extra'] ?? '');
$observacoes = trim($_POST['observacoes'] ?? '');

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // --- Validação ---------------------------------------------------------
    if ($clienteSelecionado <= 0) {
        $erros['cliente_id'] = 'Selecione o cliente.';
    }

    $itensValidos = [];
    foreach ($linhas as $i => $linha) {
        if ($linha['peca_id'] <= 0 && $linha['quantidade'] <= 0) {
            continue; // linha vazia deixada pelo utilizador
        }
        if ($linha['peca_id'] <= 0) {
            $erros["peca_$i"] = 'Selecione a peça.';
        } elseif ($linha['quantidade'] <= 0) {
            $erros["peca_$i"] = 'A quantidade deve ser pelo menos 1.';
        } else {
            // Junta quantidades da mesma peça repetida em duas linhas
            $itensValidos[$linha['peca_id']] = ($itensValidos[$linha['peca_id']] ?? 0) + $linha['quantidade'];
        }
    }
    if (!$itensValidos && !$erros) {
        $erros['itens'] = 'Adicione pelo menos uma peça à venda.';
    }

    $metodo = $pagamentos->obter($metodoSelecionado);
    if (!$metodo) {
        $erros['tipo_pagamento'] = 'Escolha o tipo de pagamento.';
    } elseif ($metodo->etiquetaCampoExtra() !== null) {
        $erroExtra = $metodo->validarCampoExtra($campoExtra);
        if ($erroExtra) {
            $erros['campo_extra'] = $erroExtra;
        }
    }

    // --- Gravação ---------------------------------------------------------
    if (!$erros) {
        try {
            $itens = [];
            $total = 0.0;
            foreach ($itensValidos as $pecaId => $qtd) {
                $itens[] = ['peca_id' => $pecaId, 'quantidade' => $qtd];
                $peca = $pecasRepo->obter($pecaId);
                $total += $peca ? (float) $peca['preco'] * $qtd : 0;
            }
            $detalhe = $metodo->detalhe($total, $campoExtra);
            $vendaId = (new VendaRepository())->registar($clienteSelecionado, $itens, $metodo->codigo(), $detalhe, $observacoes);
            flash('sucesso', "Venda nº $vendaId registada com sucesso. Pagamento: {$metodo->nome()}" . ($detalhe ? " ($detalhe)" : '') . '.');
            redirecionar("venda_detalhe.php?id=$vendaId");
        } catch (RuntimeException $e) {
            $erros['itens'] = $e->getMessage();
        }
    }
}

// Mapa id -> peça para o JavaScript mostrar preço/stock ao escolher no dropdown
$pecasJson = json_encode(array_map(fn($p) => [
    'id' => (int) $p['id'], 'referencia' => $p['referencia'], 'nome' => $p['nome'], 'marca' => $p['marca'],
    'tipo' => $p['tipo'], 'preco' => (float) $p['preco'], 'stock' => (int) $p['stock'], 'descricao' => $p['descricao'],
], $pecas), JSON_UNESCAPED_UNICODE);

require __DIR__ . '/../templates/cabecalho.php';
?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Vendas</p>
    <h1>Nova venda a cliente</h1>
    <p>Escolha o cliente, as peças e o tipo de pagamento. O stock é atualizado automaticamente.</p>
  </div>
</section>

<?php if (isset($erros['itens'])): ?>
  <div class="alerta alerta-erro"><?= e($erros['itens']) ?></div>
<?php endif; ?>

<form method="post" action="venda.php" class="formulario formulario-venda" id="form-venda" novalidate>
  <div class="duas-colunas colunas-2-1">
    <div>
      <section class="cartao">
        <h2>1. Cliente</h2>
        <label>Cliente *
          <select name="cliente_id" required>
            <option value="">— Selecione o cliente —</option>
            <?php foreach ($clientes as $c): ?>
              <option value="<?= $c['id'] ?>" <?= $c['id'] == $clienteSelecionado ? 'selected' : '' ?>><?= e($c['nome']) ?> (NIF <?= e($c['nif']) ?>)</option>
            <?php endforeach; ?>
          </select>
          <?php if (isset($erros['cliente_id'])): ?><span class="erro-campo"><?= e($erros['cliente_id']) ?></span><?php endif; ?>
        </label>
        <p class="texto-suave">Cliente novo? <a href="clientes.php">Registe-o primeiro</a>.</p>
      </section>

      <section class="cartao">
        <div class="cartao-topo">
          <h2>2. Peças</h2>
          <button type="button" class="botao botao-pequeno botao-secundario" id="adicionar-linha">+ Adicionar peça</button>
        </div>

        <div id="linhas-pecas">
          <?php foreach ($linhas as $i => $linha): ?>
          <div class="linha-peca" data-indice="<?= $i ?>">
            <label>Peça *
              <select name="peca_id[]" class="select-peca" required>
                <option value="">— Selecione a peça —</option>
                <?php foreach ($pecas as $p): ?>
                  <option value="<?= $p['id'] ?>" <?= $p['id'] == $linha['peca_id'] ? 'selected' : '' ?> <?= $p['stock'] <= 0 ? 'disabled' : '' ?>>
                    <?= e($p['referencia']) ?> · <?= e($p['marca']) ?> <?= e($p['nome']) ?> — <?= formatarPreco($p['preco']) ?> (stock <?= $p['stock'] ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>Qtd. *
              <input type="number" name="quantidade[]" class="input-quantidade" min="1" value="<?= $linha['quantidade'] ?: 1 ?>" required>
            </label>
            <button type="button" class="botao-icone remover-linha" title="Remover linha" aria-label="Remover linha">×</button>
            <div class="info-peca texto-suave"></div>
            <?php if (isset($erros["peca_$i"])): ?><span class="erro-campo"><?= e($erros["peca_$i"]) ?></span><?php endif; ?>
          </div>
          <?php endforeach; ?>
        </div>

        <template id="template-linha">
          <div class="linha-peca">
            <label>Peça *
              <select name="peca_id[]" class="select-peca" required>
                <option value="">— Selecione a peça —</option>
                <?php foreach ($pecas as $p): ?>
                  <option value="<?= $p['id'] ?>" <?= $p['stock'] <= 0 ? 'disabled' : '' ?>>
                    <?= e($p['referencia']) ?> · <?= e($p['marca']) ?> <?= e($p['nome']) ?> — <?= formatarPreco($p['preco']) ?> (stock <?= $p['stock'] ?>)
                  </option>
                <?php endforeach; ?>
              </select>
            </label>
            <label>Qtd. *
              <input type="number" name="quantidade[]" class="input-quantidade" min="1" value="1" required>
            </label>
            <button type="button" class="botao-icone remover-linha" title="Remover linha" aria-label="Remover linha">×</button>
            <div class="info-peca texto-suave"></div>
          </div>
        </template>
      </section>

      <section class="cartao">
        <h2>3. Tipo de pagamento</h2>
        <?php if (isset($erros['tipo_pagamento'])): ?><p class="erro-campo"><?= e($erros['tipo_pagamento']) ?></p><?php endif; ?>
        <div class="opcoes-pagamento">
          <?php foreach ($pagamentos->todos() as $m): ?>
            <label class="opcao-pagamento">
              <input type="radio" name="tipo_pagamento" value="<?= e($m->codigo()) ?>"
                     data-campo-extra="<?= e($m->etiquetaCampoExtra() ?? '') ?>"
                     <?= $m->codigo() === $metodoSelecionado ? 'checked' : '' ?> required>
              <span>
                <strong><?= e($m->nome()) ?></strong>
                <small><?= e($m->descricao()) ?></small>
              </span>
            </label>
          <?php endforeach; ?>
        </div>
        <label id="grupo-campo-extra" hidden>
          <span id="rotulo-campo-extra"></span>
          <input type="text" name="campo_extra" value="<?= e($campoExtra) ?>">
          <?php if (isset($erros['campo_extra'])): ?><span class="erro-campo"><?= e($erros['campo_extra']) ?></span><?php endif; ?>
        </label>
        <p class="texto-suave nota">Os métodos disponíveis são definidos em <code>php/config/pagamentos.php</code>. Para adicionar um novo basta criar uma classe que implemente <code>MetodoPagamento</code> e registá-la lá.</p>
      </section>
    </div>

    <aside class="cartao resumo-venda">
      <h2>Resumo</h2>
      <ul id="resumo-itens" class="lista-resumo"><li class="texto-suave">Nenhuma peça selecionada.</li></ul>
      <div class="total-linha"><span>Total</span><strong id="resumo-total">0,00 €</strong></div>
      <label>Observações
        <textarea name="observacoes" rows="3" maxlength="255"><?= e($observacoes) ?></textarea>
      </label>
      <button class="botao botao-largo" type="submit">Registar venda</button>
      <a class="botao botao-secundario botao-largo" href="pecas.php">Voltar ao catálogo</a>
    </aside>
  </div>
</form>

<script id="dados-pecas" type="application/json"><?= $pecasJson ?></script>
<?php require __DIR__ . '/../templates/rodape.php'; ?>
