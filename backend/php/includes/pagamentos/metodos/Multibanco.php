<?php
final class Multibanco implements MetodoPagamento
{
    public function codigo(): string
    {
        return 'multibanco';
    }

    public function nome(): string
    {
        return 'Referência Multibanco';
    }

    public function descricao(): string
    {
        return 'Gera uma referência MB válida por 48 horas para pagamento em ATM ou homebanking.';
    }

    public function confirmar(array $venda): string
    {
        $entidade = '12345';
        $referencia = str_pad((string) random_int(100000000, 999999999), 9, '0', STR_PAD_LEFT);
        return sprintf(
            'Entidade %s / Referência %s / Valor %s. Válida 48 horas.',
            $entidade,
            $referencia,
            dinheiro($venda['total'])
        );
    }
}
