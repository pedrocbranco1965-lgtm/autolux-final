<?php
final class Transferencia implements MetodoPagamento
{
    public function codigo(): string
    {
        return 'transferencia';
    }

    public function nome(): string
    {
        return 'Transferência bancária';
    }

    public function descricao(): string
    {
        return 'Transferência para o IBAN da AutoLux. A peça só é entregue após confirmação do movimento.';
    }

    public function confirmar(array $venda): string
    {
        return 'Aguardar transferência para PT50 0000 0000 0000 0000 0000 0 no valor de '
            . dinheiro($venda['total']) . '. Identificador: VENDA-' . $venda['id_temporario'];
    }
}
