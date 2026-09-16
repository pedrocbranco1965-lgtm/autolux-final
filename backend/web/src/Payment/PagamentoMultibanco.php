<?php

declare(strict_types=1);

namespace App\Payment;

/** Referência Multibanco: a venda fica pendente até o pagamento ser confirmado. */
final class PagamentoMultibanco extends MetodoPagamentoBase
{
    private const ENTIDADE = '21830';

    public function codigo(): string
    {
        return 'multibanco';
    }

    public function processar(array $dados, float $total): ResultadoPagamento
    {
        $referencia = sprintf(
            '%s %s %s %s',
            self::ENTIDADE,
            $this->digitos(3),
            $this->digitos(3),
            $this->digitos(3),
        );

        return ResultadoPagamento::pendente(
            sprintf('Referência Multibanco gerada (%s), válida durante 3 dias.', $referencia),
            $referencia,
        );
    }
}
