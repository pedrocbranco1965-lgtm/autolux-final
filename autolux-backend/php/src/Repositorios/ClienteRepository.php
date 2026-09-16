<?php
declare(strict_types=1);

namespace AutoLux\Repositorios;

use AutoLux\Database;
use PDO;

/** Acesso à tabela `clientes`. */
final class ClienteRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::ligacao();
    }

    public function listar(string $pesquisa = ''): array
    {
        $sql = 'SELECT c.*,
                       COUNT(v.id)            AS num_vendas,
                       COALESCE(SUM(v.total), 0) AS total_gasto
                  FROM clientes c
                  LEFT JOIN vendas v ON v.cliente_id = c.id';
        $params = [];
        if ($pesquisa !== '') {
            $sql .= ' WHERE c.nome LIKE :p OR c.nif LIKE :p OR c.email LIKE :p';
            $params['p'] = "%$pesquisa%";
        }
        $sql .= ' GROUP BY c.id ORDER BY c.nome';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    /** Versão leve para preencher dropdowns. */
    public function paraDropdown(): array
    {
        return $this->pdo->query('SELECT id, nome, nif FROM clientes ORDER BY nome')->fetchAll();
    }

    public function obter(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM clientes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    /** @return array<string,string> erros de validação indexados por campo (vazio = OK) */
    public function validar(array $dados): array
    {
        $erros = [];
        if (mb_strlen(trim($dados['nome'] ?? '')) < 3) {
            $erros['nome'] = 'O nome deve ter pelo menos 3 caracteres.';
        }
        if (!preg_match('/^\d{9}$/', $dados['nif'] ?? '')) {
            $erros['nif'] = 'O NIF deve ter 9 dígitos.';
        }
        if (!filter_var($dados['email'] ?? '', FILTER_VALIDATE_EMAIL)) {
            $erros['email'] = 'Introduza um email válido.';
        }
        return $erros;
    }

    public function criar(array $dados): int
    {
        $stmt = $this->pdo->prepare(
            'INSERT INTO clientes (nome, nif, email, telefone, morada)
             VALUES (:nome, :nif, :email, :telefone, :morada)'
        );
        $stmt->execute([
            'nome'     => trim($dados['nome']),
            'nif'      => $dados['nif'],
            'email'    => trim($dados['email']),
            'telefone' => trim($dados['telefone'] ?? '') ?: null,
            'morada'   => trim($dados['morada'] ?? '') ?: null,
        ]);
        return (int) $this->pdo->lastInsertId();
    }
}
