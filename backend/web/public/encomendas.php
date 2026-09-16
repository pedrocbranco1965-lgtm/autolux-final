<?php

declare(strict_types=1);

/**
 * Requisito funcional C) Submissão de encomenda a fornecedor.
 * O formulário e a listagem funcionam inteiramente sobre a web API Node.js:
 * é o serviço de compras que grava na Base de Dados 2.
 */

require dirname(__DIR__) . '/bootstrap.php';

use App\Service\ComprasService;
use App\Service\ValidacaoException;
use App\Support\Csrf;
use App\Support\Flash;
use App\Support\Http\ApiException;
use App\Support\View;

$compras = new ComprasService();
$erros = [];
$erroApi = null;
$formulario = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formulario = $_POST;

    if (!Csrf::valido($_POST['_token'] ?? null)) {
        $erros[] = 'A sessão expirou. Submeta novamente o formulário.';
    } else {
        try {
            if (($_POST['acao'] ?? '') === 'atualizar_estado') {
                $resultado = $compras->atualizarEstado(
                    (int) ($_POST['encomenda_id'] ?? 0),
                    (string) ($_POST['estado'] ?? ''),
                );
            } else {
                $resultado = $compras->submeterEncomenda($_POST);
            }

            Flash::sucesso($resultado['mensagem']);
            redirecionar('/encomendas.php');
        } catch (ValidacaoException $erro) {
            $erros = $erro->erros();
        } catch (ApiException $erro) {
            $erros[] = $erro->getMessage();
        }
    }
}

$fornecedores = [];
$encomendas = [];
$resumo = null;

$estadoFiltro = trim((string) ($_GET['estado'] ?? ''));
$fornecedorFiltro = isset($_GET['fornecedor_id']) && $_GET['fornecedor_id'] !== ''
    ? (int) $_GET['fornecedor_id']
    : null;

try {
    $fornecedores = $compras->fornecedores(apenasAtivos: true);
    $encomendas = $compras->encomendas($fornecedorFiltro, $estadoFiltro ?: null);
    $resumo = $compras->resumo();
} catch (ApiException $erro) {
    $erroApi = $erro->getMessage();
}

View::render('encomendas', [
    'titulo' => 'Encomendas a fornecedores',
    'paginaAtiva' => 'encomendas',
    'fornecedores' => $fornecedores,
    'encomendas' => $encomendas,
    'resumo' => $resumo,
    'estadoFiltro' => $estadoFiltro,
    'fornecedorFiltro' => $fornecedorFiltro,
    'formulario' => $formulario,
    'erros' => $erros,
    'erroApi' => $erroApi,
]);
