<?php
final class ClienteRepositorio
{
    public function __construct(private PDO $pdo)
    {
    }

    public function todos(): array
    {
        return $this->pdo->query('SELECT * FROM clientes ORDER BY nome')->fetchAll();
    }

    public function obter(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM clientes WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $linha = $stmt->fetch();
        return $linha ?: null;
    }

    public function contar(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM clientes')->fetchColumn();
    }
}
