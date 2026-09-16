<?php

declare(strict_types=1);

namespace App\Support\Http;

use RuntimeException;

/** Falha na comunicação com o serviço Node.js de compras. */
final class ApiException extends RuntimeException
{
    public function __construct(string $mensagem, private int $estadoHttp = 0)
    {
        parent::__construct($mensagem, $estadoHttp);
    }

    public function estadoHttp(): int
    {
        return $this->estadoHttp;
    }

    public function servicoIndisponivel(): bool
    {
        return $this->estadoHttp === 0 || $this->estadoHttp >= 500;
    }
}
