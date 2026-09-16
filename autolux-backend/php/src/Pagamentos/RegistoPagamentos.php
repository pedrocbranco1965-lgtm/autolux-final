<?php
declare(strict_types=1);

namespace AutoLux\Pagamentos;

/**
 * Coleção dos métodos de pagamento disponíveis. É preenchida a partir de
 * php/config/pagamentos.php e usada pelo formulário de venda para
 * apresentar as opções e validar o método escolhido.
 */
final class RegistoPagamentos
{
    /** @var array<string, MetodoPagamento> indexado pelo código */
    private array $metodos = [];

    /** @param class-string<MetodoPagamento>[] $classes */
    public function __construct(array $classes)
    {
        foreach ($classes as $classe) {
            $metodo = new $classe();
            if (!$metodo instanceof MetodoPagamento) {
                throw new \InvalidArgumentException("$classe não implementa MetodoPagamento");
            }
            $this->metodos[$metodo->codigo()] = $metodo;
        }
    }

    /** @return MetodoPagamento[] */
    public function todos(): array
    {
        return array_values($this->metodos);
    }

    public function obter(?string $codigo): ?MetodoPagamento
    {
        return $this->metodos[$codigo ?? ''] ?? null;
    }

    public function nomePorCodigo(string $codigo): string
    {
        return $this->metodos[$codigo]?->nome() ?? ucfirst($codigo);
    }
}
