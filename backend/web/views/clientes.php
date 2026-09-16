<?php

/**
 * @var array $clientes
 * @var array $formulario
 * @var array $erros
 */

use App\Support\Csrf;
use App\Support\Format;
?>

<div class="pagina-cabecalho">
    <div>
        <h1>Clientes</h1>
        <p>Clientes do armazém e volume de compras associado a cada um.</p>
    </div>
    <a class="botao botao--secundario" href="/nova-venda.php">Registar encomenda</a>
</div>

<?php if ($erros !== []) : ?>
    <div class="alerta alerta--erro">
        <strong>Corrija os seguintes pontos:</strong>
        <ul>
            <?php foreach ($erros as $erro) : ?>
                <li><?= e($erro) ?></li>
            <?php endforeach; ?>
        </ul>
    </div>
<?php endif; ?>

<section class="cartao">
    <h2>Novo cliente</h2>
    <form class="formulario" method="post" action="/clientes.php">
        <?= Csrf::campo() ?>
        <div class="linha-campos">
            <div class="campo">
                <label for="nome">Nome *</label>
                <input type="text" id="nome" name="nome" required value="<?= e(old($formulario, 'nome')) ?>">
            </div>
            <div class="campo">
                <label for="nif">NIF *</label>
                <input type="text" id="nif" name="nif" required maxlength="9" pattern="\d{9}"
                       value="<?= e(old($formulario, 'nif')) ?>">
            </div>
            <div class="campo">
                <label for="email">Email *</label>
                <input type="email" id="email" name="email" required value="<?= e(old($formulario, 'email')) ?>">
            </div>
            <div class="campo">
                <label for="telefone">Telefone</label>
                <input type="tel" id="telefone" name="telefone" value="<?= e(old($formulario, 'telefone')) ?>">
            </div>
            <div class="campo">
                <label for="morada">Morada</label>
                <input type="text" id="morada" name="morada" value="<?= e(old($formulario, 'morada')) ?>">
            </div>
        </div>
        <div class="acoes">
            <button class="botao" type="submit">Registar cliente</button>
        </div>
    </form>
</section>

<section class="cartao">
    <div class="cartao__titulo">
        <h2><?= e(count($clientes)) ?> cliente(s)</h2>
    </div>
    <div class="tabela-wrapper">
        <table>
            <thead>
            <tr>
                <th>Nome</th>
                <th>NIF</th>
                <th>Contactos</th>
                <th class="numerico">Vendas</th>
                <th class="numerico">Valor comprado</th>
                <th></th>
            </tr>
            </thead>
            <tbody>
            <?php foreach ($clientes as $cliente) : ?>
                <tr>
                    <td>
                        <?= e($cliente['nome']) ?>
                        <?php if (!(bool) $cliente['ativo']) : ?>
                            <span class="badge badge--erro">inativo</span>
                        <?php endif; ?>
                    </td>
                    <td><?= e($cliente['nif']) ?></td>
                    <td class="texto-suave">
                        <?= e($cliente['email']) ?><br>
                        <?= e($cliente['telefone'] ?: '—') ?>
                    </td>
                    <td class="numerico"><?= e($cliente['total_vendas']) ?></td>
                    <td class="numerico"><?= e(Format::moeda($cliente['valor_total'])) ?></td>
                    <td class="numerico">
                        <a class="botao botao--pequeno botao--secundario"
                           href="/vendas.php?cliente_id=<?= e($cliente['id']) ?>">Ver vendas</a>
                    </td>
                </tr>
            <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</section>
