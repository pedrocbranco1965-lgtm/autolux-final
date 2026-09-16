<?php

declare(strict_types=1);

/**
 * Endpoint JSON interno que reencaminha para o serviço Node.js o catálogo de um
 * fornecedor. O browser fala sempre com o PHP, e é o PHP que integra a web API
 * de compras — mantendo a API Node.js como camada intermédia do módulo de compras.
 */

require dirname(__DIR__, 2) . '/bootstrap.php';

use App\Service\ComprasService;
use App\Support\Http\ApiException;

header('Content-Type: application/json; charset=utf-8');

$id = isset($_GET['id']) ? (int) $_GET['id'] : 0;

if ($id <= 0) {
    http_response_code(400);
    echo json_encode(['erro' => 'Indique o identificador do fornecedor.'], JSON_UNESCAPED_UNICODE);

    return;
}

try {
    $artigos = (new ComprasService())->artigosDoFornecedor($id);
    echo json_encode(['total' => count($artigos), 'dados' => $artigos], JSON_UNESCAPED_UNICODE);
} catch (ApiException $erro) {
    http_response_code($erro->servicoIndisponivel() ? 503 : $erro->estadoHttp());
    echo json_encode(['erro' => $erro->getMessage()], JSON_UNESCAPED_UNICODE);
}
