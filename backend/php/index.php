<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

$pecasRepo = PecaFactory::criar();
$clientesRepo = new ClienteRepositorio(Database::vendas());
$vendasServico = new VendaServico(Database::vendas());
$api = new FornecedorApi(API_FORNECEDORES_URL);

$todasPecas = $pecasRepo->listar(null, null, null, null);
$stockBaixo = array_values(array_filter($todasPecas, fn ($p) => (int) $p['stock'] <= 8));
$apiOk = $api->saude();

$nFornecedores = 0;
$nEncomendas = 0;
if ($apiOk) {
    try {
        $nFornecedores = count($api->fornecedores());
        $nEncomendas = count($api->encomendas());
    } catch (Throwable) {
        $apiOk = false;
    }
}

layout_inicio('Início', 'inicio');
?>
<section class="hero">
    <div>
        <p class="eyebrow">Armazém de peças</p>
        <h1>Bem-vindo, <?= e(utilizador_atual()['nome']) ?>.</h1>
        <p class="muted">Consulte o stock, registe vendas a clientes e encomende peças aos fornecedores através da API Node.js.</p>
    </div>
    <a class="btn btn-primary" href="venda.php">Nova venda</a>
</section>

<section class="stats">
    <article class="stat">
        <span>Peças em catálogo</span>
        <strong><?= count($todasPecas) ?></strong>
    </article>
    <article class="stat">
        <span>Clientes</span>
        <strong><?= $clientesRepo->contar() ?></strong>
    </article>
    <article class="stat">
        <span>Vendas registadas</span>
        <strong><?= $vendasServico->contar() ?></strong>
    </article>
    <article class="stat">
        <span>Faturação</span>
        <strong><?= dinheiro($vendasServico->totalFaturado()) ?></strong>
    </article>
    <article class="stat">
        <span>Fornecedores (API)</span>
        <strong><?= $apiOk ? $nFornecedores : '—' ?></strong>
    </article>
    <article class="stat">
        <span>Encomendas (API)</span>
        <strong><?= $apiOk ? $nEncomendas : '—' ?></strong>
    </article>
</section>

<?php if (!$apiOk): ?>
    <div class="flash flash-aviso">A API Node.js de fornecedores não está a responder em <?= e(API_FORNECEDORES_URL) ?>. Arranque-a com <code>npm start</code> na pasta <code>backend</code>.</div>
<?php endif; ?>

<div class="grid-2">
    <section class="card">
        <div class="card-head">
            <h2>Stock baixo</h2>
            <a href="pecas.php">Ver catálogo</a>
        </div>
        <?php if (!$stockBaixo): ?>
            <p class="muted">Nenhuma peça abaixo de 8 unidades.</p>
        <?php else: ?>
            <ul class="lista-simples">
                <?php foreach (array_slice($stockBaixo, 0, 6) as $peca): ?>
                    <li>
                        <span><?= e($peca['marca']) ?> · <?= e($peca['nome']) ?></span>
                        <strong class="<?= (int) $peca['stock'] <= 3 ? 'perigo' : 'aviso' ?>"><?= (int) $peca['stock'] ?> un.</strong>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
    <section class="card">
        <div class="card-head">
            <h2>Últimas vendas</h2>
            <a href="vendas.php">Ver todas</a>
        </div>
        <?php $recentes = $vendasServico->recentes(6); ?>
        <?php if (!$recentes): ?>
            <p class="muted">Ainda não há vendas. Use <a href="venda.php">Nova venda</a>.</p>
        <?php else: ?>
            <ul class="lista-simples">
                <?php foreach ($recentes as $venda): ?>
                    <li>
                        <span><?= e($venda['cliente_nome']) ?> · <?= e($venda['peca_nome']) ?></span>
                        <strong><?= dinheiro($venda['total']) ?></strong>
                    </li>
                <?php endforeach; ?>
            </ul>
        <?php endif; ?>
    </section>
</div>
<?php layout_fim(); ?>
