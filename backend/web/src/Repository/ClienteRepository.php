<?php

declare(strict_types=1);

namespace App\Repository;

use App\Support\Database;
use PDO;

/** Acesso à tabela de clientes da Base de Dados 1. */
final class ClienteRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::ligacao();
    }

    /** @return array<int, array<string, mixed>> */
    public function listar(bool $apenasAtivos = false): array
    {
        $sql = 'SELECT * FROM clientes';
        if ($apenasAtivos) {
            $sql .= ' WHERE ativo = 1';
        }

        return $this->pdo->query($sql . ' ORDER BY nome')->fetchAll();
    }

    /** Clientes com o total já comprado, usado na página de clientes. */
    public function listarComTotais(): array
    {
        return $this->pdo->query(
            'SELECT c.*,
                    COUNT(v.id)                  AS total_vendas,
                    COALESCE(SUM(v.total), 0)    AS valor_total
             FROM clientes c
                      LEFT JOIN vendas v ON v.cliente_id = c.id AND v.estado <> "anulada"
             GROUP BY c.id
             ORDER BY c.nome',
        )->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public function procurarPorId(int $id): ?array
    {
        $consulta = $this->pdo->prepare('SELECT * FROM clientes WHERE id = :id');
        $consulta->execute(['id' => $id]);

        return $consulta->fetch() ?: null;
    }

    public function existeNif(string $nif): bool
    {
        $consulta = $this->pdo->prepare('SELECT 1 FROM clientes WHERE nif = :nif');
        $consulta->execute(['nif' => $nif]);

        return (bool) $consulta->fetchColumn();
    }

    /** @param array<string, string|null> $dados */
    public function criar(array $dados): int
    {
        $consulta = $this->pdo->prepare(
            'INSERT INTO clientes (nome, nif, email, telefone, morada)
             VALUES (:nome, :nif, :email, :telefone, :morada)',
        );
        $consulta->execute([
            'nome' => $dados['nome'],
            'nif' => $dados['nif'],
            'email' => $dados['email'],
            'telefone' => $dados['telefone'] ?: null,
            'morada' => $dados['morada'] ?: null,
        ]);

        return (int) $this->pdo->lastInsertId();
    }

    public function total(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM clientes WHERE ativo = 1')->fetchColumn();
    }
}
