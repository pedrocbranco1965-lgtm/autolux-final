<?php
require_once __DIR__ . '/env.php';
carregarEnv();
require_once __DIR__ . '/config.php';
require_once __DIR__ . '/helpers.php';
require_once __DIR__ . '/db.php';
require_once __DIR__ . '/auth.php';
require_once __DIR__ . '/http.php';
require_once __DIR__ . '/pecas/PecaRepositorioInterface.php';
require_once __DIR__ . '/pecas/PecaRepositorioMysql.php';
require_once __DIR__ . '/pecas/PecaRepositorioJson.php';
require_once __DIR__ . '/pecas/PecaRepositorioApi.php';
require_once __DIR__ . '/pecas/PecaFactory.php';
require_once __DIR__ . '/clientes/ClienteRepositorio.php';
require_once __DIR__ . '/vendas/VendaServico.php';
require_once __DIR__ . '/pagamentos/MetodoPagamento.php';
require_once __DIR__ . '/pagamentos/metodos/Numerario.php';
require_once __DIR__ . '/pagamentos/metodos/Multibanco.php';
require_once __DIR__ . '/pagamentos/metodos/MbWay.php';
require_once __DIR__ . '/pagamentos/metodos/Transferencia.php';
require_once __DIR__ . '/pagamentos/metodos/Cartao.php';
require_once __DIR__ . '/pagamentos/CatalogoPagamentos.php';
require_once __DIR__ . '/fornecedores/FornecedorApi.php';
require_once __DIR__ . '/layout.php';

if (session_status() !== PHP_SESSION_ACTIVE) {
    session_start();
}
