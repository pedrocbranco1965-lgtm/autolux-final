<?php
/**
 * Painel inicial: indicadores rápidos das duas bases de dados
 * (BD1 via PDO, BD2 via API Node.js) e atalhos para as funcionalidades.
 */
require __DIR__ . '/../src/bootstrap.php';

use AutoLux\Repositorios\PecaRepository;
use AutoLux\Repositorios\ClienteRepository;
use AutoLux\Repositorios\VendaRepository;

$titulo = 'Painel';

try {
    $pecas = new PecaRepository();
    $numPecas = count($pecas->listar());
    $stockBaixo = $pecas->listar(['so_stock_baixo' => '1']);
    $numClientes = count((new ClienteRepository())->paraDropdown());
    $resumoVendas = (new VendaRepository())->resumo();
    $ultimasVendas = (new VendaRepository())->listar(5);
} catch (Throwable $excecao) {
    $titulo = 'Base de dados indisponível';
    require __DIR__ . '/../templates/erro.php';
    exit;
}

// A API pode estar em baixo sem impedir o resto da página de funcionar
$apiOk = $apiFornecedores->saudavel();
$encomendasPendentes = [];
if ($apiOk) {
    try {
        $encomendasPendentes = $apiFornecedores->listarEncomendas(['estado' => 'pendente']);
    } catch (Throwable) {
        $apiOk = false;
    }
}

require __DIR__ . '/../templates/cabecalho.php';
?>
<section class="cabecalho-pagina">
  <div>
    <p class="rotulo">Gestão de armazém</p>
    <h1>Bem-vindo à AutoLux</h1>
    <p>Consulte stock e clientes, registe vendas e encomende peças aos fornecedores.</p>
  </div>
  <a href="venda.php" class="botao">+ Nova venda</a>
</section>

<section class="grelha-indicadores">
  <a class="indicador" href="pecas.php">
    <span class="indicador-valor"><?= $numPecas ?></span>
    <span class="indicador-nome">Peças em catálogo</span>
  </a>
  <a class="indicador <?= $stockBaixo ? 'indicador-aviso' : '' ?>" href="pecas.php?so_stock_baixo=1">
    <span class="indicador-valor"><?= count($stockBaixo) ?></span>
    <span class="indicador-nome">Peças com stock baixo</span>
  </a>
  <a class="indicador" href="clientes.php">
    <span class="indicador-valor"><?= $numClientes ?></span>
    <span class="indicador-nome">Clientes</span>
  </a>
  <a class="indicador" href="vendas.php">
    <span class="indicador-valor"><?= formatarPreco($resumoVendas['faturacao']) ?></span>
    <span class="indicador-nome"><?= $resumoVendas['num_vendas'] ?> vendas · hoje <?= formatarPreco($resumoVendas['faturacao_hoje']) ?></span>
  </a>
  <a class="indicador <?= $apiOk ? '' : 'indicador-erro' ?>" href="encomendas_fornecedor.php">
    <span class="indicador-valor"><?= $apiOk ? count($encomendasPendentes) : '—' ?></span>
    <span class="indicador-nome"><?= $apiOk ? 'Encomendas pendentes (API Node.js)' : 'API Node.js indisponível' ?></span>
  </a>
</section>

<div class="duas-colunas">
  <section class="cartao">
    <div class="cartao-topo">
      <h2>Últimas vendas</h2>
      <a href="vendas.php">Ver todas</a>
    </div>
    <?php if (!$ultimasVendas): ?>
      <p class="texto-suave">Ainda não há vendas registadas.</p>
    <?php else: ?>
    <table class="tabela">
      <thead><tr><th>#</th><th>Cliente</th><th>Data</th><th>Pagamento</th><th class="num">Total</th></tr></thead>
      <tbody>
        <?php foreach ($ultimasVendas as $v): ?>
        <tr>
          <td><a href="venda_detalhe.php?id=<?= $v['id'] ?>">#<?= $v['id'] ?></a></td>
          <td><?= e($v['cliente']) ?></td>
          <td><?= formatarData($v['data_venda']) ?></td>
          <td><?= e($pagamentos->nomePorCodigo($v['tipo_pagamento'])) ?></td>
          <td class="num"><?= formatarPreco($v['total']) ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </section>

  <section class="cartao">
    <div class="cartao-topo">
      <h2>Stock abaixo do mínimo</h2>
      <a href="encomenda_fornecedor.php">Encomendar</a>
    </div>
    <?php if (!$stockBaixo): ?>
      <p class="texto-suave">Todas as peças estão acima do stock mínimo.</p>
    <?php else: ?>
    <table class="tabela">
      <thead><tr><th>Referência</th><th>Peça</th><th class="num">Stock</th><th class="num">Mínimo</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($stockBaixo as $p): ?>
        <tr>
          <td><code><?= e($p['referencia']) ?></code></td>
          <td><?= e($p['marca']) ?> <?= e($p['nome']) ?></td>
          <td class="num texto-erro"><?= $p['stock'] ?></td>
          <td class="num"><?= $p['stock_minimo'] ?></td>
          <td><a class="botao botao-pequeno botao-secundario" href="encomenda_fornecedor.php?peca_id=<?= $p['id'] ?>">Encomendar</a></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
    <?php endif; ?>
  </section>
</div>

<section class="cartao arquitetura">
  <h2>Como está montado</h2>
  <div class="arquitetura-fluxo">
    <div class="bloco"><strong>Interface PHP</strong><span>páginas dinâmicas (esta app)</span></div>
    <div class="seta">PDO →</div>
    <div class="bloco"><strong>BD1 MySQL</strong><span>clientes · peças · vendas</span></div>
    <div class="seta">cURL/HTTP →</div>
    <div class="bloco"><strong>API Node.js</strong><span>Express · /api/fornecedores · /api/encomendas</span></div>
    <div class="seta">mysql2 →</div>
    <div class="bloco"><strong>BD2 MySQL</strong><span>fornecedores · encomendas</span></div>
  </div>
</section>
<?php require __DIR__ . '/../templates/rodape.php'; ?>
