<?php

declare(strict_types=1);

namespace App\Payment;

/** Cartão de crédito/débito processado no terminal de pagamento do balcão. */
final class PagamentoCartao extends MetodoPagamentoBase
{
    public function codigo(): string
    {
        return 'cartao';
    }

    public function campos(): array
    {
        return [
            [
                'nome' => 'ultimos_digitos',
                'etiqueta' => 'Últimos 4 dígitos do cartão',
                'tipo' => 'text',
                'exemplo' => '4321',
                'obrigatorio' => true,
                'ajuda' => 'Apenas os últimos 4 dígitos são guardados no registo da venda.',
            ],
        ];
    }

    public function validar(array $dados): array
    {
        $erros = parent::validar($dados);
        $digitos = $this->valor($dados, 'ultimos_digitos');

        if ($digitos !== '' && preg_match('/^\d{4}$/', $digitos) !== 1) {
            $erros[] = 'Indique exatamente os 4 últimos dígitos do cartão.';
        }

        return $erros;
    }

    public function processar(array $dados, float $total): ResultadoPagamento
    {
        $referencia = 'TPA-' . $this->digitos(6) . '-' . $this->valor($dados, 'ultimos_digitos');

        return ResultadoPagamento::pago('Pagamento autorizado no terminal (TPA).', $referencia);
    }
}
