<?php

declare(strict_types=1);

namespace App\Service;

use App\Support\Http\ApiClient;
use App\Support\Http\ApiException;

/**
 * Ponte entre a interface de gestão e o serviço Node.js de compras.
 * Os dados de fornecedores e encomendas nunca são lidos diretamente da Base de
 * Dados 2: passam sempre pela web API, como pede o enunciado.
 */
final class ComprasService
{
    public function __construct(private ApiClient $api = new ApiClient())
    {
    }

    /** @return array<int, array<string, mixed>> */
    public function fornecedores(?string $pesquisa = null, ?string $pais = null, bool $apenasAtivos = false): array
    {
        $resposta = $this->api->get('/fornecedores', [
            'q' => $pesquisa,
            'pais' => $pais,
            'ativos' => $apenasAtivos ? 'true' : null,
        ]);

        return $resposta['dados'] ?? [];
    }

    /** @return array<int, string> */
    public function paises(): array
    {
        return $this->api->get('/fornecedores/paises')['dados'] ?? [];
    }

    /** @return array<string, mixed> */
    public function fornecedor(int $id): array
    {
        return $this->api->get("/fornecedores/{$id}")['dados'] ?? [];
    }

    /** @return array<int, array<string, mixed>> */
    public function artigosDoFornecedor(int $id): array
    {
        return $this->api->get("/fornecedores/{$id}/artigos")['dados'] ?? [];
    }

    /** @return array<int, array<string, mixed>> */
    public function encomendas(?int $fornecedorId = null, ?string $estado = null): array
    {
        $resposta = $this->api->get('/encomendas', [
            'fornecedorId' => $fornecedorId,
            'estado' => $estado,
        ]);

        return $resposta['dados'] ?? [];
    }

    /** @return array<string, mixed> */
    public function encomenda(int $id): array
    {
        return $this->api->get("/encomendas/{$id}")['dados'] ?? [];
    }

    /** @return array<string, mixed> */
    public function resumo(): array
    {
        return $this->api->get('/encomendas/resumo')['dados'] ?? [];
    }

    /**
     * Submete uma encomenda ao fornecedor através da API.
     *
     * @param array<string, mixed> $formulario
     * @return array{mensagem: string, encomenda: array<string, mixed>}
     *
     * @throws ValidacaoException quando faltam dados obrigatórios no formulário
     * @throws ApiException quando o serviço Node.js recusa ou está inacessível
     */
    public function submeterEncomenda(array $formulario): array
    {
        $fornecedorId = (int) ($formulario['fornecedor_id'] ?? 0);
        if ($fornecedorId <= 0) {
            throw new ValidacaoException(['Selecione o fornecedor a quem vai encomendar.']);
        }

        $itens = [];
        foreach ($formulario['itens'] ?? [] as $linha) {
            $quantidade = (int) ($linha['quantidade'] ?? 0);
            $referencia = trim((string) ($linha['referencia'] ?? ''));

            if ($referencia === '' || $quantidade <= 0) {
                continue;
            }

            $itens[] = [
                'referencia' => $referencia,
                'designacao' => trim((string) ($linha['designacao'] ?? $referencia)),
                'quantidade' => $quantidade,
                'precoUnitario' => (float) ($linha['preco_unitario'] ?? 0),
            ];
        }

        if ($itens === []) {
            throw new ValidacaoException(['Adicione pelo menos um artigo à encomenda.']);
        }

        $resposta = $this->api->post('/encomendas', [
            'fornecedorId' => $fornecedorId,
            'dataPrevista' => trim((string) ($formulario['data_prevista'] ?? '')) ?: null,
            'observacoes' => trim((string) ($formulario['observacoes'] ?? '')) ?: null,
            'itens' => $itens,
        ]);

        return [
            'mensagem' => (string) ($resposta['mensagem'] ?? 'Encomenda submetida.'),
            'encomenda' => $resposta['dados'] ?? [],
        ];
    }

    /** @return array<string, mixed> */
    public function atualizarEstado(int $encomendaId, string $estado): array
    {
        $resposta = $this->api->patch("/encomendas/{$encomendaId}/estado", ['estado' => $estado]);

        return [
            'mensagem' => (string) ($resposta['mensagem'] ?? 'Estado atualizado.'),
            'encomenda' => $resposta['dados'] ?? [],
        ];
    }

    public function servicoDisponivel(): bool
    {
        return $this->api->disponivel();
    }
}
