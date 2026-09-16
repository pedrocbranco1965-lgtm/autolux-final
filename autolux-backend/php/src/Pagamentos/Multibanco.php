<?php
declare(strict_types=1);

namespace AutoLux\Pagamentos;

/** Gera uma referência Multibanco (simulada) para o cliente pagar. */
final class Multibanco implements MetodoPagamento
{
    public function codigo(): string { return 'multibanco'; }
    public function nome(): string { return 'Referência Multibanco'; }
    public function descricao(): string { return 'É gerada uma referência para pagamento em ATM ou homebanking.'; }
    public function etiquetaCampoExtra(): ?string { return null; }
    public function validarCampoExtra(?string $valor): ?string { return null; }

    public function detalhe(float $total, ?string $valorExtra): ?string
    {
        $referencia = str_pad((string) random_int(0, 999999999), 9, '0', STR_PAD_LEFT);
        return sprintf('Entidade 21 000 | Ref. %s %s %s | %.2f €',
            substr($referencia, 0, 3), substr($referencia, 3, 3), substr($referencia, 6, 3), $total);
    }
}
