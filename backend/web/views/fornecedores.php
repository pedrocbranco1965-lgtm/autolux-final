<?php

/**
 * @var array $fornecedores
 * @var array $paises
 * @var string $pesquisa
 * @var string $pais
 * @var bool $apenasAtivos
 * @var string|null $erroApi
 */
?>

<div class="pagina-cabecalho">
    <div>
        <h1>Fornecedores</h1>
        <p>Lista obtida por integração com a web API Node.js (Base de Dados 2).</p>
    </div>
    <a class="botao" href="/encomendas.php">Submeter encomenda</a>
</div>

<?php if ($erroApi !== null) : ?>
    <div class="alerta alerta--erro">
        <strong>Não foi possível obter os fornecedores.</strong>
        <p><?= e($erroApi) ?></p>
        <p class="texto-suave">
            O serviço de compras corre em Node.js. Arranque-o com <code>npm start</code>
            (ou <code>npm run start:api</code>) e volte a carregar esta página.
        </p>
    </div>
<?php else : ?>

    <form class="cartao" method="get" action="/fornecedores.php">
        <div class="linha-campos">
            <div class="campo">
                <label for="q">Pesquisar</label>
                <input type="search" id="q" name="q" placeholder="Nome, email ou NIF" value="<?= e($pesquisa) ?>">
            </div>
            <div class="campo">
                <label for="pais">País</label>
                <select id="pais" name="pais">
                    <option value="">Todos os países</option>
                    <?php foreach ($paises as $opcao) : ?>
                        <option value="<?= e($opcao) ?>" <?= $pais === $opcao ? 'selected' : '' ?>><?= e($opcao) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="campo" style="justify-content:flex-end">
                <label class="texto-suave">
                    <input type="checkbox" name="ativos" value="1" style="width:auto" <?= $apenasAtivos ? 'checked' : '' ?>>
                    Apenas fornecedores ativos
                </label>
            </div>
        </div>
        <div class="acoes espacamento-topo">
            <button class="botao" type="submit">Filtrar</button>
            <a class="botao botao--secundario" href="/fornecedores.php">Limpar</a>
        </div>
    </form>

    <section class="cartao">
        <div class="cartao__titulo">
            <h2><?= e(count($fornecedores)) ?> fornecedor(es)</h2>
            <span class="texto-suave">GET /api/fornecedores</span>
        </div>

        <?php if ($fornecedores === []) : ?>
            <p class="vazio">Nenhum fornecedor corresponde aos filtros.</p>
        <?php else : ?>
            <div class="tabela-wrapper">
                <table>
                    <thead>
                    <tr>
                        <th>Fornecedor</th>
                        <th>NIF</th>
                        <th>Contactos</th>
                        <th>País</th>
                        <th class="numerico">Prazo</th>
                        <th class="numerico">Encomendas</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                    </thead>
                    <tbody>
                    <?php foreach ($fornecedores as $fornecedor) : ?>
                        <tr>
                            <td><?= e($fornecedor['nome']) ?></td>
                            <td><?= e($fornecedor['nif']) ?></td>
                            <td class="texto-suave">
                                <?= e($fornecedor['email']) ?><br>
                                <?= e($fornecedor['telefone'] ?: '—') ?>
                            </td>
                            <td><?= e($fornecedor['pais']) ?></td>
                            <td class="numerico"><?= e($fornecedor['prazoEntregaDias']) ?> dias</td>
                            <td class="numerico"><?= e($fornecedor['totalEncomendas']) ?></td>
                            <td>
                                <?php if ((int) $fornecedor['ativo'] === 1) : ?>
                                    <span class="badge badge--ok">ativo</span>
                                <?php else : ?>
                                    <span class="badge badge--erro">inativo</span>
                                <?php endif; ?>
                            </td>
                            <td class="numerico">
                                <?php if ((int) $fornecedor['ativo'] === 1) : ?>
                                    <a class="botao botao--pequeno"
                                       href="/encomendas.php?fornecedor_id=<?= e($fornecedor['id']) ?>">Encomendar</a>
                                <?php endif; ?>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php endif; ?>
    </section>
<?php endif; ?>
