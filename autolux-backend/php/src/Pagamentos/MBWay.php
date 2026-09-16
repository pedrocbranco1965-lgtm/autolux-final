<?php
declare(strict_types=1);

namespace AutoLux\Pagamentos;

final class MBWay implements MetodoPagamento
{
    public function codigo(): string { return 'mbway'; }
    public function nome(): string { return 'MB WAY'; }
    public function descricao(): string { return 'Pedido de pagamento enviado para o telemóvel do cliente.'; }
    public function etiquetaCampoExtra(): ?string { return 'Nº de telemóvel (9 dígitos)'; }

    public function validarCampoExtra(?string $valor): ?string
    {
        $valor = preg_replace('/\s+/', '', (string) $valor);
        return preg_match('/^9\d{8}$/', $valor) ? null : 'Indique um número de telemóvel português válido (9 dígitos, começa por 9).';
    }

    public function detalhe(float $total, ?string $valorExtra): ?string
    {
        return 'MB WAY para ' . preg_replace('/\s+/', '', (string) $valorExtra);
    }
}
