<?php

declare(strict_types=1);

namespace App\Payment;

/** Resultado do processamento de um pagamento. */
final class ResultadoPagamento
{
    public function __construct(
        public readonly string $estadoVenda,
        public readonly ?string $referencia,
        public readonly string $mensagem,
    ) {
    }

    public static function pago(string $mensagem, ?string $referencia = null): self
    {
        return new self('paga', $referencia, $mensagem);
    }

    public static function pendente(string $mensagem, ?string $referencia = null): self
    {
        return new self('pendente', $referencia, $mensagem);
    }
}
