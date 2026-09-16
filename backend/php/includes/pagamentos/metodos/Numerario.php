<?php
final class Numerario implements MetodoPagamento
{
    public function codigo(): string
    {
        return 'numerario';
    }

    public function nome(): string
    {
        return 'Numerário';
    }

    public function descricao(): string
    {
        return 'Pagamento em numerário no balcão, com emissão de recibo imediato.';
    }

    public function confirmar(array $venda): string
    {
        return 'Recebido em numerário no valor de ' . dinheiro($venda['total']) . '. Recibo emitido no ato.';
    }
}
