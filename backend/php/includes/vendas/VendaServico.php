<?php
final class VendaServico
{
    public function __construct(private PDO $pdo)
    {
    }

    public function recentes(int $limite = 8): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT v.*, c.nome AS cliente_nome, p.nome AS peca_nome, p.referencia
             FROM vendas v
             INNER JOIN clientes c ON c.id = v.cliente_id
             INNER JOIN pecas p ON p.id = v.peca_id
             ORDER BY v.criado_em DESC
             LIMIT :limite'
        );
        $stmt->bindValue('limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function todas(): array
    {
        return $this->pdo->query(
            'SELECT v.*, c.nome AS cliente_nome, p.nome AS peca_nome, p.referencia, p.marca
             FROM vendas v
             INNER JOIN clientes c ON c.id = v.cliente_id
             INNER JOIN pecas p ON p.id = v.peca_id
             ORDER BY v.criado_em DESC'
        )->fetchAll();
    }

    public function contar(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM vendas')->fetchColumn();
    }

    public function totalFaturado(): float
    {
        return (float) $this->pdo->query('SELECT COALESCE(SUM(total), 0) FROM vendas')->fetchColumn();
    }

    /**
     * Regista a venda na BD1 e abate o stock. Tudo numa transação:
     * se o stock não chegar, não fica venda a meio.
     */
    public function registar(int $clienteId, int $pecaId, int $quantidade, MetodoPagamento $pagamento): int
    {
        if ($quantidade < 1) {
            throw new InvalidArgumentException('A quantidade tem de ser pelo menos 1.');
        }

        $this->pdo->beginTransaction();
        try {
            $stmtPeca = $this->pdo->prepare('SELECT * FROM pecas WHERE id = :id FOR UPDATE');
            $stmtPeca->execute(['id' => $pecaId]);
            $peca = $stmtPeca->fetch();
            if (!$peca) {
                throw new RuntimeException('Peça não encontrada na base de dados de stock.');
            }
            if ((int) $peca['stock'] < $quantidade) {
                throw new RuntimeException('Stock insuficiente. Disponível: ' . $peca['stock'] . ' unidade(s).');
            }

            $cliente = (new ClienteRepositorio($this->pdo))->obter($clienteId);
            if (!$cliente) {
                throw new RuntimeException('Cliente não encontrado.');
            }

            $preco = (float) $peca['preco'];
            $total = $preco * $quantidade;
            $contexto = [
                'total' => $total,
                'cliente_telefone' => $cliente['telefone'],
                'id_temporario' => strtoupper(substr(sha1((string) microtime(true)), 0, 6)),
            ];
            $notas = $pagamento->confirmar($contexto);

            $insert = $this->pdo->prepare(
                'INSERT INTO vendas (cliente_id, peca_id, quantidade, preco_unitario, total, tipo_pagamento, notas_pagamento, estado)
                 VALUES (:cliente_id, :peca_id, :quantidade, :preco_unitario, :total, :tipo_pagamento, :notas, :estado)'
            );
            $insert->execute([
                'cliente_id' => $clienteId,
                'peca_id' => $pecaId,
                'quantidade' => $quantidade,
                'preco_unitario' => $preco,
                'total' => $total,
                'tipo_pagamento' => $pagamento->codigo(),
                'notas' => $notas,
                'estado' => 'registada',
            ]);
            $id = (int) $this->pdo->lastInsertId();

            $update = $this->pdo->prepare('UPDATE pecas SET stock = stock - :qtd WHERE id = :id');
            $update->execute(['qtd' => $quantidade, 'id' => $pecaId]);

            $this->pdo->commit();
            return $id;
        } catch (Throwable $erro) {
            $this->pdo->rollBack();
            throw $erro;
        }
    }
}
