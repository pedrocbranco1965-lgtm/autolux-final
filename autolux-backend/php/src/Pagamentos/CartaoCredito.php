<?php
declare(strict_types=1);

namespace AutoLux\Pagamentos;

final class CartaoCredito implements MetodoPagamento
{
    public function codigo(): string { return 'cartao'; }
    public function nome(): string { return 'Cartão de crédito / débito'; }
    public function descricao(): string { return 'Pagamento no terminal TPA da loja.'; }
    public function etiquetaCampoExtra(): ?string { return 'Últimos 4 dígitos do cartão'; }

    public function validarCampoExtra(?string $valor): ?string
    {
        return preg_match('/^\d{4}$/', (string) $valor) ? null : 'Indique exatamente os últimos 4 dígitos do cartão.';
    }

    public function detalhe(float $total, ?string $valorExtra): ?string
    {
        return 'Cartão terminado em ' . $valorExtra;
    }
}
