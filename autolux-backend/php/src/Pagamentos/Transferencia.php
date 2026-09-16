<?php
declare(strict_types=1);

namespace AutoLux\Pagamentos;

final class Transferencia implements MetodoPagamento
{
    public function codigo(): string { return 'transferencia'; }
    public function nome(): string { return 'Transferência bancária'; }
    public function descricao(): string { return 'Para clientes empresariais com conta corrente. Pagamento a 30 dias.'; }
    public function etiquetaCampoExtra(): ?string { return null; }
    public function validarCampoExtra(?string $valor): ?string { return null; }

    public function detalhe(float $total, ?string $valorExtra): ?string
    {
        $vencimento = (new \DateTimeImmutable('+30 days'))->format('d/m/Y');
        return 'IBAN PT50 0000 0000 0000 0000 0000 0 | vencimento ' . $vencimento;
    }
}
