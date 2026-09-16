<?php
final class MbWay implements MetodoPagamento
{
    public function codigo(): string
    {
        return 'mbway';
    }

    public function nome(): string
    {
        return 'MB WAY';
    }

    public function descricao(): string
    {
        return 'Pedido de pagamento enviado para o número de telemóvel associado ao cliente.';
    }

    public function confirmar(array $venda): string
    {
        $telefone = $venda['cliente_telefone'] ?? 'número do cliente';
        return 'Pedido MB WAY enviado para ' . $telefone . ' no valor de ' . dinheiro($venda['total']) . '.';
    }
}
