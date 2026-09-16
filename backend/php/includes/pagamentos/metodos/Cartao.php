<?php
final class Cartao implements MetodoPagamento
{
    public function codigo(): string
    {
        return 'cartao';
    }

    public function nome(): string
    {
        return 'Cartão de débito/crédito';
    }

    public function descricao(): string
    {
        return 'Pagamento no TPA do balcão (Visa, Mastercard, Multibanco).';
    }

    public function confirmar(array $venda): string
    {
        $autorizacao = strtoupper(bin2hex(random_bytes(3)));
        return 'Pagamento TPA autorizado. Código ' . $autorizacao . '. Valor ' . dinheiro($venda['total']) . '.';
    }
}
