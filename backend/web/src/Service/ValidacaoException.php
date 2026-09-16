<?php

declare(strict_types=1);

namespace App\Service;

use RuntimeException;

/** Agrupa os erros de validação de um formulário numa única exceção. */
final class ValidacaoException extends RuntimeException
{
    /** @param array<int, string> $erros */
    public function __construct(private array $erros)
    {
        parent::__construct(implode(' ', $erros));
    }

    /** @return array<int, string> */
    public function erros(): array
    {
        return $this->erros;
    }
}
