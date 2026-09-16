<?php
/**
 * Endpoint REST só de leitura usado pela Fonte B do catálogo.
 * Lê sempre da Base de Dados 1 (nunca chama o factory, para não criar um ciclo).
 */
require_once __DIR__ . '/includes/env.php';
carregarEnv();
require_once __DIR__ . '/includes/config.php';
require_once __DIR__ . '/includes/db.php';
require_once __DIR__ . '/includes/pecas/PecaRepositorioInterface.php';
require_once __DIR__ . '/includes/pecas/PecaRepositorioMysql.php';

header('Content-Type: application/json; charset=utf-8');
header('Access-Control-Allow-Origin: *');

$repo = new PecaRepositorioMysql(Database::vendas());

if (isset($_GET['id'])) {
    $peca = $repo->obter((int) $_GET['id']);
    echo json_encode(['peca' => $peca], JSON_UNESCAPED_UNICODE);
    exit;
}

if (isset($_GET['meta'])) {
    echo json_encode([
        'marcas' => $repo->marcas(),
        'tipos' => $repo->tipos(),
    ], JSON_UNESCAPED_UNICODE);
    exit;
}

$marca = isset($_GET['marca']) && $_GET['marca'] !== '' ? (string) $_GET['marca'] : null;
$tipo = isset($_GET['tipo']) && $_GET['tipo'] !== '' ? (string) $_GET['tipo'] : null;
$precoMin = isset($_GET['preco_min']) && $_GET['preco_min'] !== '' ? (float) $_GET['preco_min'] : null;
$precoMax = isset($_GET['preco_max']) && $_GET['preco_max'] !== '' ? (float) $_GET['preco_max'] : null;

echo json_encode([
    'fonte' => 'mysql',
    'pecas' => $repo->listar($marca, $tipo, $precoMin, $precoMax),
], JSON_UNESCAPED_UNICODE);
