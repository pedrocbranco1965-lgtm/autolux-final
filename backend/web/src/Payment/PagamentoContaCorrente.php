<?php

declare(strict_types=1);

namespace App\Payment;

/** Conta corrente: venda a crédito, faturada ao cliente empresarial no fim do prazo. */
final class PagamentoContaCorrente extends MetodoPagamentoBase
{
    private const LIMITE_SEM_APROVACAO = 1000.00;

    public function codigo(): string
    {
        return 'conta_corrente';
    }

    public function campos(): array
    {
        return [
            [
                'nome' => 'prazo_dias',
                'etiqueta' => 'Prazo de pagamento (dias)',
                'tipo' => 'number',
                'exemplo' => '30',
                'obrigatorio' => true,
                'ajuda' => 'Prazos aceites: 15, 30, 60 ou 90 dias.',
            ],
        ];
    }

    public function validar(array $dados): array
    {
        $erros = parent::validar($dados);
        $prazo = $this->valor($dados, 'prazo_dias');

        if ($prazo !== '' && !in_array((int) $prazo, [15, 30, 60, 90], true)) {
            $erros[] = 'O prazo de pagamento tem de ser 15, 30, 60 ou 90 dias.';
        }

        return $erros;
    }

    public function processar(array $dados, float $total): ResultadoPagamento
    {
        $prazo = (int) $this->valor($dados, 'prazo_dias');
        $vencimento = date('d/m/Y', strtotime("+{$prazo} days"));
        $referencia = 'CC-' . date('Y') . '-' . $this->digitos(4);

        $mensagem = sprintf('Venda a crédito registada. Vencimento a %s.', $vencimento);
        if ($total > self::LIMITE_SEM_APROVACAO) {
            $mensagem .= sprintf(
                ' Acima de %.2f €, precisa de aprovação da direção financeira.',
                self::LIMITE_SEM_APROVACAO,
            );
        }

        return ResultadoPagamento::pendente($mensagem, $referencia);
    }
}
