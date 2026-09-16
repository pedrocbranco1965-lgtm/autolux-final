<?php

declare(strict_types=1);

namespace App\Payment;

/**
 * Método usado quando existe uma linha em metodos_pagamento sem classe dedicada.
 * Permite que o armazém acrescente um método novo só com um INSERT: o pagamento
 * fica pendente e pode receber uma classe própria mais tarde, sem alterar páginas.
 */
final class PagamentoGenerico extends MetodoPagamentoBase
{
    public function __construct(private string $codigo, private string $designacao)
    {
    }

    public function codigo(): string
    {
        return $this->codigo;
    }

    public function campos(): array
    {
        return [
            [
                'nome' => 'referencia_manual',
                'etiqueta' => 'Referência do pagamento (opcional)',
                'tipo' => 'text',
                'exemplo' => 'Nº do talão ou do comprovativo',
                'obrigatorio' => false,
            ],
        ];
    }

    public function processar(array $dados, float $total): ResultadoPagamento
    {
        $referencia = $this->valor($dados, 'referencia_manual');

        return ResultadoPagamento::pendente(
            sprintf('Venda registada com o método "%s" e ficou pendente de confirmação.', $this->designacao),
            $referencia !== '' ? $referencia : null,
        );
    }
}
