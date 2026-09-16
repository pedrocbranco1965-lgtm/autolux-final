<?php
/** Histórico de vendas registadas (BD1). */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Repositorios\VendaRepository;

$titulo = 'Vendas';

try {
    $repo = new VendaRepository();
    $lista = $repo->listar(200);
    $resumo = $repo->resumo();
} catch (Throwable $excecao) {
    $titulo = 'Base de dados indisponível';
    require __DIR__ . '/../templates/erro.php';
    exit;
}

require __DIR__ . '/../templates/cabecalho.php';
?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Vendas</p>
    <h1>Histórico de vendas</h1>
    <p><?= $resumo['num_vendas'] ?> venda(s) · faturação total <?= formatarPreco($resumo['faturacao']) ?></p>
  </div>
  <a href="venda.php" class="botao">+ Nova venda</a>
</section>

<section class="cartao">
  <table class="tabela">
    <thead>
      <tr><th>#</th><th>Data</th><th>Cliente</th><th>Funcionário</th><th>Pagamento</th><th class="num">Linhas</th><th class="num">Unidades</th><th class="num">Total</th><th></th></tr>
    </thead>
    <tbody>
      <?php if (!$lista): ?>
        <tr><td colspan="9" class="texto-suave">Ainda não há vendas registadas.</td></tr>
      <?php endif; ?>
      <?php foreach ($lista as $v): ?>
      <tr>
        <td>#<?= $v['id'] ?></td>
        <td><?= formatarData($v['data_venda']) ?></td>
        <td><?= e($v['cliente']) ?></td>
        <td><?= e($v['funcionario'] ?? '—') ?></td>
        <td><?= e($pagamentos->nomePorCodigo($v['tipo_pagamento'])) ?><br><small class="texto-suave"><?= e($v['detalhe_pagamento']) ?></small></td>
        <td class="num"><?= $v['num_itens'] ?></td>
        <td class="num"><?= $v['unidades'] ?></td>
        <td class="num"><strong><?= formatarPreco($v['total']) ?></strong></td>
        <td><a class="botao botao-pequeno botao-secundario" href="venda_detalhe.php?id=<?= $v['id'] ?>">Detalhe</a></td>
      </tr>
      <?php endforeach; ?>
    </tbody>
  </table>
</section>
<?php require __DIR__ . '/../templates/rodape.php'; ?>
