<?php

declare(strict_types=1);

namespace App\Payment;

/** Pagamento em dinheiro no balcão: fica imediatamente liquidado. */
final class PagamentoNumerario extends MetodoPagamentoBase
{
    public function codigo(): string
    {
        return 'numerario';
    }

    public function campos(): array
    {
        return [
            [
                'nome' => 'valor_entregue',
                'etiqueta' => 'Valor entregue pelo cliente (€)',
                'tipo' => 'number',
                'exemplo' => '50.00',
                'obrigatorio' => true,
                'ajuda' => 'Serve para calcular o troco a devolver.',
            ],
        ];
    }

    public function validar(array $dados): array
    {
        $erros = parent::validar($dados);
        $entregue = $this->valor($dados, 'valor_entregue');

        if ($entregue !== '' && !is_numeric($entregue)) {
            $erros[] = 'O valor entregue tem de ser numérico.';
        }

        return $erros;
    }

    public function processar(array $dados, float $total): ResultadoPagamento
    {
        $entregue = (float) $this->valor($dados, 'valor_entregue');

        if ($entregue + 0.001 < $total) {
            return ResultadoPagamento::pendente(
                sprintf('Valor entregue insuficiente: faltam %.2f €. A venda ficou pendente.', $total - $entregue),
            );
        }

        return ResultadoPagamento::pago(
            sprintf('Pagamento em numerário recebido. Troco a devolver: %.2f €.', $entregue - $total),
        );
    }
}
