<?php

declare(strict_types=1);

/**
 * Requisito funcional B) Formulário de encomenda de peça para cliente.
 * Dropdown de clientes, informação da peça e tipo de pagamento escolhido de
 * entre os métodos ativos na base de dados (facilmente extensíveis).
 */

require dirname(__DIR__) . '/bootstrap.php';

use App\Service\ValidacaoException;
use App\Service\VendaService;
use App\Support\Csrf;
use App\Support\Flash;
use App\Support\View;

$vendas = new VendaService();
$formulario = [];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formulario = $_POST;

    if (!Csrf::valido($_POST['_token'] ?? null)) {
        $erros[] = 'A sessão expirou. Submeta novamente o formulário.';
    } else {
        try {
            $resultado = $vendas->registarEncomenda($_POST);

            Flash::sucesso('Encomenda registada com sucesso. ' . $resultado['mensagem']);
            redirecionar('/venda.php?id=' . $resultado['venda_id']);
        } catch (ValidacaoException $erro) {
            $erros = $erro->erros();
        } catch (RuntimeException $erro) {
            $erros[] = $erro->getMessage();
        }
    }
}

View::render('nova-venda', [
    'titulo' => 'Nova encomenda de cliente',
    'paginaAtiva' => 'nova-venda',
    'clientes' => $vendas->clientesDisponiveis(),
    'pecas' => $vendas->pecasDisponiveis(),
    'metodos' => $vendas->metodosPagamento(),
    'camposPorMetodo' => $vendas->camposPorMetodo(),
    'pecaSelecionada' => isset($_GET['peca_id']) ? (int) $_GET['peca_id'] : 0,
    'formulario' => $formulario,
    'erros' => $erros,
]);
