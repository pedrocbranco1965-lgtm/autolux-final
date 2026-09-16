<?php

declare(strict_types=1);

namespace App\Payment;

/** Comportamento comum aos métodos de pagamento (campos e validações básicas). */
abstract class MetodoPagamentoBase implements MetodoPagamento
{
    public function campos(): array
    {
        return [];
    }

    public function validar(array $dados): array
    {
        $erros = [];

        foreach ($this->campos() as $campo) {
            $valor = trim((string) ($dados[$campo['nome']] ?? ''));
            if ($campo['obrigatorio'] && $valor === '') {
                $erros[] = sprintf('Preencha o campo "%s".', $campo['etiqueta']);
            }
        }

        return $erros;
    }

    /** @param array<string, string> $dados */
    protected function valor(array $dados, string $campo): string
    {
        return trim((string) ($dados[$campo] ?? ''));
    }

    /** Sequência aleatória usada nas referências geradas pelos vários métodos. */
    protected function digitos(int $quantidade): string
    {
        return str_pad((string) random_int(0, 10 ** $quantidade - 1), $quantidade, '0', STR_PAD_LEFT);
    }
}
