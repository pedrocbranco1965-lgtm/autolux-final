<?php

declare(strict_types=1);

namespace App\Payment;

/**
 * Fábrica que converte uma linha da tabela metodos_pagamento na classe que sabe
 * tratar esse pagamento.
 *
 * Para acrescentar um método de pagamento novo:
 *   1. INSERT INTO metodos_pagamento (codigo, designacao, ...) VALUES ('paypal', ...);
 *   2. criar App\Payment\PagamentoPaypal e acrescentar uma entrada ao mapa abaixo.
 * O passo 2 é opcional: sem classe própria, o método funciona através de
 * PagamentoGenerico. Nenhuma página PHP precisa de ser alterada nos dois casos.
 */
final class RegistoMetodosPagamento
{
    /** @var array<string, class-string<MetodoPagamento>> */
    private const MAPA = [
        'numerario' => PagamentoNumerario::class,
        'multibanco' => PagamentoMultibanco::class,
        'mbway' => PagamentoMbWay::class,
        'cartao' => PagamentoCartao::class,
        'transferencia' => PagamentoTransferencia::class,
        'conta_corrente' => PagamentoContaCorrente::class,
    ];

    /** @param array<string, mixed> $metodo Linha da tabela metodos_pagamento. */
    public static function para(array $metodo): MetodoPagamento
    {
        $codigo = (string) $metodo['codigo'];
        $classe = self::MAPA[$codigo] ?? null;

        if ($classe === null) {
            return new PagamentoGenerico($codigo, (string) $metodo['designacao']);
        }

        return new $classe();
    }

    public static function temClasseDedicada(string $codigo): bool
    {
        return isset(self::MAPA[$codigo]);
    }
}
