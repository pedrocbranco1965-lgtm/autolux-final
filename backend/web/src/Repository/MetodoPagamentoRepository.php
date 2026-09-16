<?php

declare(strict_types=1);

namespace App\Repository;

use App\Support\Database;
use PDO;

/**
 * Métodos de pagamento disponíveis no formulário de encomenda.
 * Acrescentar um novo método é apenas um INSERT nesta tabela: a página PHP
 * constrói o dropdown a partir do que aqui estiver ativo.
 */
final class MetodoPagamentoRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::ligacao();
    }

    /** @return array<int, array<string, mixed>> */
    public function listarAtivos(): array
    {
        return $this->pdo
            ->query('SELECT * FROM metodos_pagamento WHERE ativo = 1 ORDER BY ordem, designacao')
            ->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public function procurarPorId(int $id): ?array
    {
        $consulta = $this->pdo->prepare('SELECT * FROM metodos_pagamento WHERE id = :id AND ativo = 1');
        $consulta->execute(['id' => $id]);

        return $consulta->fetch() ?: null;
    }
}
