<?php
/**
 * Cliente HTTP da API Node.js (Base de Dados 2).
 * O PHP nunca abre uma ligação MySQL a fornecedores — só fala REST.
 */
final class FornecedorApi
{
    public function __construct(private string $baseUrl)
    {
    }

    public function fornecedores(): array
    {
        return http_get_json($this->baseUrl . '/api/fornecedores');
    }

    public function encomendas(): array
    {
        return http_get_json($this->baseUrl . '/api/encomendas');
    }

    public function criarEncomenda(array $dados): array
    {
        return http_post_json($this->baseUrl . '/api/encomendas', $dados);
    }

    public function saude(): bool
    {
        try {
            $dados = http_get_json($this->baseUrl . '/api/health');
            return !empty($dados['ok']);
        } catch (Throwable) {
            return false;
        }
    }
}
