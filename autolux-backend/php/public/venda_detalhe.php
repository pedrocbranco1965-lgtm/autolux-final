<?php
/** Detalhe/recibo de uma venda. */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Repositorios\VendaRepository;

$titulo = 'Detalhe da venda';
$id = (int) ($_GET['id'] ?? 0);

try {
    $venda = $id > 0 ? (new VendaRepository())->obter($id) : null;
} catch (Throwable $excecao) {
    $titulo = 'Base de dados indisponível';
    require __DIR__ . '/../templates/erro.php';
    exit;
}

require __DIR__ . '/../templates/cabecalho.php';

if (!$venda): ?>
  <section class="cartao vazio">
    <h1>Venda não encontrada</h1>
    <p>Não existe nenhuma venda com o número <?= $id ?>.</p>
    <a class="botao" href="vendas.php">Voltar às vendas</a>
  </section>
<?php else: ?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Venda</p>
    <h1>Venda nº <?= $venda['id'] ?></h1>
    <p><?= formatarData($venda['data_venda']) ?></p>
  </div>
  <div class="acoes">
    <a href="venda.php?cliente_id=<?= $venda['cliente_id'] ?>" class="botao botao-secundario">Nova venda a este cliente</a>
    <a href="vendas.php" class="botao botao-secundario">Todas as vendas</a>
  </div>
</section>

<div class="duas-colunas colunas-2-1">
  <section class="cartao">
    <h2>Peças vendidas</h2>
    <table class="tabela">
      <thead><tr><th>Referência</th><th>Peça</th><th class="num">Qtd.</th><th class="num">Preço unit.</th><th class="num">Subtotal</th></tr></thead>
      <tbody>
        <?php foreach ($venda['itens'] as $i): ?>
        <tr>
          <td><code><?= e($i['referencia']) ?></code></td>
          <td><?= e($i['peca']) ?></td>
          <td class="num"><?= $i['quantidade'] ?></td>
          <td class="num"><?= formatarPreco($i['preco_unitario']) ?></td>
          <td class="num"><?= formatarPreco($i['subtotal']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
      <tfoot><tr><th colspan="4" class="num">Total</th><th class="num"><?= formatarPreco($venda['total']) ?></th></tr></tfoot>
    </table>
  </section>

  <aside>
    <section class="cartao">
      <h2>Cliente</h2>
      <p><strong><?= e($venda['cliente']) ?></strong><br>NIF <?= e($venda['cliente_nif']) ?><br><?= e($venda['cliente_email']) ?></p>
    </section>
    <section class="cartao">
      <h2>Pagamento</h2>
      <p><strong><?= e($pagamentos->nomePorCodigo($venda['tipo_pagamento'])) ?></strong></p>
      <?php if ($venda['detalhe_pagamento']): ?><p class="texto-suave"><?= e($venda['detalhe_pagamento']) ?></p><?php endif; ?>
      <?php if ($venda['observacoes']): ?><p><em><?= e($venda['observacoes']) ?></em></p><?php endif; ?>
    </section>
  </aside>
</div>
<?php endif; ?>
<?php require __DIR__ . '/../templates/rodape.php'; ?>
