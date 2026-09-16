<?php
declare(strict_types=1);

namespace AutoLux\Pagamentos;

final class Dinheiro implements MetodoPagamento
{
    public function codigo(): string { return 'dinheiro'; }
    public function nome(): string { return 'Dinheiro'; }
    public function descricao(): string { return 'Pagamento em numerário ao balcão.'; }
    public function etiquetaCampoExtra(): ?string { return null; }
    public function validarCampoExtra(?string $valor): ?string { return null; }
    public function detalhe(float $total, ?string $valorExtra): ?string { return 'Pago em numerário'; }
}
