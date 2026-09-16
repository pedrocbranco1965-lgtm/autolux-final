<?php

declare(strict_types=1);

/** Gestão de clientes: listagem com totais comprados e registo de novos clientes. */

require dirname(__DIR__) . '/bootstrap.php';

use App\Repository\ClienteRepository;
use App\Support\Csrf;
use App\Support\Flash;
use App\Support\View;

$clientes = new ClienteRepository();
$formulario = [];
$erros = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $formulario = $_POST;

    if (!Csrf::valido($_POST['_token'] ?? null)) {
        $erros[] = 'A sessão expirou. Submeta novamente o formulário.';
    } else {
        $nome = trim((string) ($_POST['nome'] ?? ''));
        $nif = trim((string) ($_POST['nif'] ?? ''));
        $email = trim((string) ($_POST['email'] ?? ''));

        if ($nome === '') {
            $erros[] = 'Indique o nome do cliente.';
        }
        if (preg_match('/^\d{9}$/', $nif) !== 1) {
            $erros[] = 'O NIF tem de ter exatamente 9 dígitos.';
        } elseif ($clientes->existeNif($nif)) {
            $erros[] = 'Já existe um cliente registado com esse NIF.';
        }
        if (filter_var($email, FILTER_VALIDATE_EMAIL) === false) {
            $erros[] = 'Indique um endereço de email válido.';
        }

        if ($erros === []) {
            $clientes->criar([
                'nome' => $nome,
                'nif' => $nif,
                'email' => $email,
                'telefone' => trim((string) ($_POST['telefone'] ?? '')),
                'morada' => trim((string) ($_POST['morada'] ?? '')),
            ]);

            Flash::sucesso(sprintf('Cliente "%s" registado com sucesso.', $nome));
            redirecionar('/clientes.php');
        }
    }
}

View::render('clientes', [
    'titulo' => 'Clientes',
    'paginaAtiva' => 'clientes',
    'clientes' => $clientes->listarComTotais(),
    'formulario' => $formulario,
    'erros' => $erros,
]);
