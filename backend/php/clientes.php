<?php
require_once __DIR__ . '/includes/bootstrap.php';
exigir_login();

$clientes = (new ClienteRepositorio(Database::vendas()))->todos();

layout_inicio('Clientes', 'clientes');
?>
<section class="hero">
    <div>
        <p class="eyebrow">Base de Dados 1</p>
        <h1>Clientes</h1>
        <p class="muted">Oficinas e particulares cadastrados para vendas no balcão.</p>
    </div>
</section>

<div class="table-wrap card">
    <table>
        <thead>
            <tr>
                <th>Nome</th>
                <th>Email</th>
                <th>Telefone</th>
                <th>NIF</th>
                <th>Morada</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($clientes as $cliente): ?>
                <tr>
                    <td><?= e($cliente['nome']) ?></td>
                    <td><?= e($cliente['email']) ?></td>
                    <td><?= e($cliente['telefone']) ?></td>
                    <td><?= e($cliente['nif']) ?></td>
                    <td><?= e($cliente['morada']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>
<?php layout_fim(); ?>
