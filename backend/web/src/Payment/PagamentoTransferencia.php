<?php

declare(strict_types=1);

namespace App\Payment;

/** Transferência bancária: fica pendente até chegar o comprovativo. */
final class PagamentoTransferencia extends MetodoPagamentoBase
{
    private const IBAN_AUTOLUX = 'PT50 0002 0123 1234 5678 9015 4';

    public function codigo(): string
    {
        return 'transferencia';
    }

    public function campos(): array
    {
        return [
            [
                'nome' => 'iban_cliente',
                'etiqueta' => 'IBAN de origem do cliente',
                'tipo' => 'text',
                'exemplo' => 'PT50 0000 0000 0000 0000 0000 0',
                'obrigatorio' => true,
                'ajuda' => 'Transferir para o IBAN AutoLux ' . self::IBAN_AUTOLUX,
            ],
        ];
    }

    public function validar(array $dados): array
    {
        $erros = parent::validar($dados);
        $iban = str_replace(' ', '', $this->valor($dados, 'iban_cliente'));

        if ($iban !== '' && preg_match('/^PT50\d{21}$/', strtoupper($iban)) !== 1) {
            $erros[] = 'O IBAN indicado não é um IBAN português válido (PT50 + 21 dígitos).';
        }

        return $erros;
    }

    public function processar(array $dados, float $total): ResultadoPagamento
    {
        $referencia = 'TRF-' . date('Ymd') . '-' . $this->digitos(4);

        return ResultadoPagamento::pendente(
            'Transferência registada. A venda fica pendente até à receção do comprovativo.',
            $referencia,
        );
    }
}
