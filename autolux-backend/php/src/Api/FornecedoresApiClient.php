<?php
declare(strict_types=1);

namespace AutoLux\Api;

/**
 * Cliente HTTP (cURL) para a Web API Node.js de fornecedores/encomendas.
 *
 * O PHP nunca liga diretamente à Base de Dados 2: todos os dados de
 * fornecedores e encomendas passam por esta classe -> HTTP -> Node.js -> MySQL.
 */
final class FornecedoresApiClient
{
    public function __construct(private string $baseUrl)
    {
        $this->baseUrl = rtrim($baseUrl, '/');
    }

    // ----- Fornecedores ---------------------------------------------------

    public function listarFornecedores(bool $incluirInativos = false): array
    {
        return $this->pedido('GET', '/fornecedores' . ($incluirInativos ? '?todos=1' : ''));
    }

    public function obterFornecedor(int $id): array
    {
        return $this->pedido('GET', "/fornecedores/$id");
    }

    public function criarFornecedor(array $dados): array
    {
        return $this->pedido('POST', '/fornecedores', $dados);
    }

    // ----- Encomendas -----------------------------------------------------

    public function listarEncomendas(array $filtros = []): array
    {
        $query = http_build_query(array_filter($filtros, fn($v) => $v !== '' && $v !== null));
        return $this->pedido('GET', '/encomendas' . ($query ? "?$query" : ''));
    }

    public function obterEncomenda(int $id): array
    {
        return $this->pedido('GET', "/encomendas/$id");
    }

    /**
     * @param array<int, array{referencia_peca:string, descricao:string, quantidade:int, preco_unitario:float}> $itens
     */
    public function submeterEncomenda(int $fornecedorId, array $itens, ?string $observacoes = null): array
    {
        return $this->pedido('POST', '/encomendas', [
            'fornecedor_id' => $fornecedorId,
            'observacoes'   => $observacoes,
            'itens'         => $itens,
        ]);
    }

    public function alterarEstado(int $id, string $estado): array
    {
        return $this->pedido('PATCH', "/encomendas/$id/estado", ['estado' => $estado]);
    }

    public function saudavel(): bool
    {
        try {
            return ($this->pedido('GET', '/health')['estado'] ?? '') === 'ok';
        } catch (\Throwable) {
            return false;
        }
    }

    // ----- Interno ----------------------------------------------------------

    /**
     * Executa o pedido HTTP e devolve o JSON já descodificado.
     * Erros da API (4xx/5xx) são convertidos em ApiException com a mensagem da API.
     */
    private function pedido(string $metodo, string $caminho, ?array $corpo = null): array
    {
        $ch = curl_init($this->baseUrl . $caminho);
        $opcoes = [
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_CUSTOMREQUEST  => $metodo,
            CURLOPT_TIMEOUT        => 8,
            CURLOPT_CONNECTTIMEOUT => 3,
            CURLOPT_HTTPHEADER     => ['Accept: application/json', 'Content-Type: application/json'],
        ];
        if ($corpo !== null) {
            $opcoes[CURLOPT_POSTFIELDS] = json_encode($corpo, JSON_UNESCAPED_UNICODE);
        }
        curl_setopt_array($ch, $opcoes);

        $resposta = curl_exec($ch);
        $erroCurl = curl_error($ch);
        $status = (int) curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
        curl_close($ch);

        if ($resposta === false) {
            throw new ApiException(
                'Não foi possível contactar a API de fornecedores em ' . $this->baseUrl .
                '. Confirme que o serviço Node.js está a correr (npm run start:api). Detalhe: ' . $erroCurl,
                0
            );
        }

        $dados = json_decode($resposta, true);
        if (!is_array($dados)) {
            throw new ApiException('Resposta inválida da API (não é JSON).', $status);
        }

        if ($status >= 400) {
            $mensagem = $dados['erro'] ?? 'Erro na API';
            if (!empty($dados['detalhes'])) {
                $mensagem .= ': ' . implode('; ', (array) $dados['detalhes']);
            }
            throw new ApiException($mensagem, $status);
        }

        return $dados;
    }
}
