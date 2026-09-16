<?php

declare(strict_types=1);

namespace App\Payment;

/** MB WAY: exige o número de telemóvel para onde é enviado o pedido de pagamento. */
final class PagamentoMbWay extends MetodoPagamentoBase
{
    public function codigo(): string
    {
        return 'mbway';
    }

    public function campos(): array
    {
        return [
            [
                'nome' => 'telemovel',
                'etiqueta' => 'Telemóvel associado ao MB WAY',
                'tipo' => 'tel',
                'exemplo' => '912345678',
                'obrigatorio' => true,
                'ajuda' => 'Nove dígitos, começado por 9.',
            ],
        ];
    }

    public function validar(array $dados): array
    {
        $erros = parent::validar($dados);
        $telemovel = $this->valor($dados, 'telemovel');

        if ($telemovel !== '' && preg_match('/^9\d{8}$/', $telemovel) !== 1) {
            $erros[] = 'O telemóvel MB WAY tem de ter 9 dígitos e começar por 9.';
        }

        return $erros;
    }

    public function processar(array $dados, float $total): ResultadoPagamento
    {
        $telemovel = $this->valor($dados, 'telemovel');

        return ResultadoPagamento::pendente(
            sprintf('Pedido MB WAY de %.2f € enviado para %s. Aguarda confirmação do cliente.', $total, $telemovel),
            'MBWAY-' . $telemovel,
        );
    }
}
