<?php

declare(strict_types=1);

namespace App\Service;

use App\Payment\RegistoMetodosPagamento;
use App\Repository\ClienteRepository;
use App\Repository\MetodoPagamentoRepository;
use App\Repository\PecaRepository;
use App\Repository\VendaRepository;
use App\Support\Texto;

/**
 * Backend de vendas: valida a encomenda de peças de um cliente, delega o
 * pagamento no método escolhido e regista tudo na Base de Dados 1.
 */
final class VendaService
{
    public function __construct(
        private ClienteRepository $clientes = new ClienteRepository(),
        private PecaRepository $pecas = new PecaRepository(),
        private MetodoPagamentoRepository $metodos = new MetodoPagamentoRepository(),
        private VendaRepository $vendas = new VendaRepository(),
    ) {
    }

    /**
     * Regista a encomenda submetida no formulário.
     *
     * @param array<string, mixed> $formulario
     * @return array{venda_id: int, mensagem: string}
     *
     * @throws ValidacaoException quando os dados do formulário não são válidos
     */
    public function registarEncomenda(array $formulario): array
    {
        $erros = [];

        $cliente = $this->validarCliente($formulario['cliente_id'] ?? null, $erros);
        $metodo = $this->validarMetodoPagamento($formulario['metodo_pagamento_id'] ?? null, $erros);
        $itens = $this->validarItens($formulario['itens'] ?? [], $erros);

        if ($erros !== []) {
            throw new ValidacaoException($erros);
        }

        $total = array_sum(array_map(
            static fn (array $item): float => $item['quantidade'] * $item['preco_unitario'],
            $itens,
        ));

        $processador = RegistoMetodosPagamento::para($metodo);
        $errosPagamento = $processador->validar($formulario['pagamento'] ?? []);
        if ($errosPagamento !== []) {
            throw new ValidacaoException($errosPagamento);
        }

        $resultado = $processador->processar($formulario['pagamento'] ?? [], $total);

        $vendaId = $this->vendas->registar(
            (int) $cliente['id'],
            (int) $metodo['id'],
            $resultado->referencia,
            $resultado->estadoVenda,
            $this->texto($formulario['observacoes'] ?? null, 255),
            $itens,
        );

        return [
            'venda_id' => $vendaId,
            'mensagem' => $resultado->mensagem,
        ];
    }

    /** @return array<int, array<string, mixed>> */
    public function clientesDisponiveis(): array
    {
        return $this->clientes->listar(apenasAtivos: true);
    }

    /** @return array<int, array<string, mixed>> */
    public function pecasDisponiveis(): array
    {
        return $this->pecas->listarParaVenda();
    }

    /** @return array<int, array<string, mixed>> */
    public function metodosPagamento(): array
    {
        return $this->metodos->listarAtivos();
    }

    /**
     * Campos extra de cada método de pagamento, para o formulário os mostrar
     * dinamicamente conforme a opção escolhida no dropdown.
     *
     * @return array<string, array<int, array<string, mixed>>>
     */
    public function camposPorMetodo(): array
    {
        $campos = [];

        foreach ($this->metodosPagamento() as $metodo) {
            $campos[(string) $metodo['id']] = [
                'codigo' => $metodo['codigo'],
                'descricao' => $metodo['descricao'],
                'campos' => RegistoMetodosPagamento::para($metodo)->campos(),
            ];
        }

        return $campos;
    }

    /** @param array<int, string> $erros */
    private function validarCliente(mixed $clienteId, array &$erros): array
    {
        $cliente = $clienteId ? $this->clientes->procurarPorId((int) $clienteId) : null;

        if ($cliente === null) {
            $erros[] = 'Selecione um cliente válido.';

            return ['id' => 0];
        }

        return $cliente;
    }

    /** @param array<int, string> $erros */
    private function validarMetodoPagamento(mixed $metodoId, array &$erros): array
    {
        $metodo = $metodoId ? $this->metodos->procurarPorId((int) $metodoId) : null;

        if ($metodo === null) {
            $erros[] = 'Selecione um tipo de pagamento válido.';

            return ['id' => 0, 'codigo' => '', 'designacao' => ''];
        }

        return $metodo;
    }

    /**
     * Valida as linhas da encomenda e substitui o preço enviado pelo browser
     * pelo preço atual em base de dados.
     *
     * @param array<int, array<string, mixed>> $linhas
     * @param array<int, string> $erros
     * @return array<int, array{peca_id: int, quantidade: int, preco_unitario: float}>
     */
    private function validarItens(mixed $linhas, array &$erros): array
    {
        if (!is_array($linhas) || $linhas === []) {
            $erros[] = 'Adicione pelo menos uma peça à encomenda.';

            return [];
        }

        $itens = [];

        foreach ($linhas as $linha) {
            $pecaId = (int) ($linha['peca_id'] ?? 0);
            $quantidade = (int) ($linha['quantidade'] ?? 0);

            if ($pecaId <= 0) {
                continue;
            }

            $peca = $this->pecas->procurarPorId($pecaId);
            if ($peca === null) {
                $erros[] = "A peça #{$pecaId} já não existe no catálogo.";
                continue;
            }
            if ($quantidade <= 0) {
                $erros[] = sprintf('A quantidade de "%s" tem de ser pelo menos 1.', $peca['designacao']);
                continue;
            }

            // Linhas repetidas da mesma peça são somadas antes de validar o stock.
            $itens[$pecaId]['peca_id'] = $pecaId;
            $itens[$pecaId]['quantidade'] = ($itens[$pecaId]['quantidade'] ?? 0) + $quantidade;
            $itens[$pecaId]['preco_unitario'] = (float) $peca['preco'];

            if ($itens[$pecaId]['quantidade'] > (int) $peca['stock']) {
                $erros[] = sprintf(
                    'Stock insuficiente para "%s": pedidas %d unidades, disponíveis %d.',
                    $peca['designacao'],
                    $itens[$pecaId]['quantidade'],
                    (int) $peca['stock'],
                );
            }
        }

        if ($itens === [] && $erros === []) {
            $erros[] = 'Adicione pelo menos uma peça à encomenda.';
        }

        return array_values($itens);
    }

    private function texto(mixed $valor, int $maximo): ?string
    {
        $texto = trim((string) ($valor ?? ''));

        return $texto === '' ? null : Texto::limitar($texto, $maximo);
    }
}
