<?php
declare(strict_types=1);

namespace AutoLux\Repositorios;

use AutoLux\Database;
use PDO;
use RuntimeException;

/**
 * Registo e consulta de vendas (tabelas `vendas` e `vendas_itens`).
 * O registo de uma venda também abate o stock das peças vendidas.
 */
final class VendaRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::ligacao();
    }

    /**
     * Regista uma venda com um ou mais itens dentro de uma transação:
     * cabeçalho + linhas + atualização de stock ficam gravados todos, ou nenhum.
     *
     * @param array<int, array{peca_id:int, quantidade:int}> $itens
     * @return int id da venda criada
     * @throws RuntimeException se não houver stock suficiente
     */
    public function registar(int $clienteId, array $itens, string $tipoPagamento, ?string $detalhePagamento, ?string $observacoes, ?int $funcionarioId = null): int
    {
        $this->pdo->beginTransaction();
        try {
            $total = 0.0;
            $linhas = [];

            // Bloqueia as linhas das peças (FOR UPDATE) para evitar que duas vendas
            // simultâneas vendam o mesmo stock.
            $stmtPeca = $this->pdo->prepare('SELECT id, nome, preco, stock FROM pecas WHERE id = :id FOR UPDATE');
            foreach ($itens as $item) {
                $stmtPeca->execute(['id' => $item['peca_id']]);
                $peca = $stmtPeca->fetch();
                if (!$peca) {
                    throw new RuntimeException('Peça inexistente (id ' . $item['peca_id'] . ').');
                }
                if ($peca['stock'] < $item['quantidade']) {
                    throw new RuntimeException(sprintf(
                        'Stock insuficiente para "%s": disponível %d, pedido %d.',
                        $peca['nome'], $peca['stock'], $item['quantidade']
                    ));
                }
                $linhas[] = ['peca' => $peca, 'quantidade' => $item['quantidade']];
                $total += (float) $peca['preco'] * $item['quantidade'];
            }

            $stmtVenda = $this->pdo->prepare(
                'INSERT INTO vendas (cliente_id, funcionario_id, tipo_pagamento, detalhe_pagamento, total, observacoes)
                 VALUES (:cliente_id, :funcionario_id, :tipo_pagamento, :detalhe_pagamento, :total, :observacoes)'
            );
            $stmtVenda->execute([
                'cliente_id'        => $clienteId,
                'funcionario_id'    => $funcionarioId,
                'tipo_pagamento'    => $tipoPagamento,
                'detalhe_pagamento' => $detalhePagamento,
                'total'             => number_format($total, 2, '.', ''),
                'observacoes'       => $observacoes ?: null,
            ]);
            $vendaId = (int) $this->pdo->lastInsertId();

            $stmtItem = $this->pdo->prepare(
                'INSERT INTO vendas_itens (venda_id, peca_id, quantidade, preco_unitario)
                 VALUES (:venda_id, :peca_id, :quantidade, :preco_unitario)'
            );
            $stmtStock = $this->pdo->prepare('UPDATE pecas SET stock = stock - :qtd WHERE id = :id');

            foreach ($linhas as $linha) {
                $stmtItem->execute([
                    'venda_id'       => $vendaId,
                    'peca_id'        => $linha['peca']['id'],
                    'quantidade'     => $linha['quantidade'],
                    'preco_unitario' => $linha['peca']['preco'],
                ]);
                $stmtStock->execute(['qtd' => $linha['quantidade'], 'id' => $linha['peca']['id']]);
            }

            $this->pdo->commit();
            return $vendaId;
        } catch (\Throwable $e) {
            $this->pdo->rollBack();
            throw $e;
        }
    }

    public function listar(int $limite = 50): array
    {
        $stmt = $this->pdo->prepare(
            'SELECT v.*, c.nome AS cliente, f.nome AS funcionario,
                    COUNT(i.id) AS num_itens, COALESCE(SUM(i.quantidade), 0) AS unidades
               FROM vendas v
               JOIN clientes c ON c.id = v.cliente_id
               LEFT JOIN funcionarios f ON f.id = v.funcionario_id
               LEFT JOIN vendas_itens i ON i.venda_id = v.id
              GROUP BY v.id
              ORDER BY v.data_venda DESC, v.id DESC
              LIMIT :limite'
        );
        $stmt->bindValue('limite', $limite, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll();
    }

    public function obter(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT v.*, c.nome AS cliente, c.nif AS cliente_nif, c.email AS cliente_email, f.nome AS funcionario
               FROM vendas v
               JOIN clientes c ON c.id = v.cliente_id
               LEFT JOIN funcionarios f ON f.id = v.funcionario_id
              WHERE v.id = :id'
        );
        $stmt->execute(['id' => $id]);
        $venda = $stmt->fetch();
        if (!$venda) {
            return null;
        }

        $stmtItens = $this->pdo->prepare(
            'SELECT i.*, p.nome AS peca, p.referencia, (i.quantidade * i.preco_unitario) AS subtotal
               FROM vendas_itens i JOIN pecas p ON p.id = i.peca_id
              WHERE i.venda_id = :id ORDER BY i.id'
        );
        $stmtItens->execute(['id' => $id]);
        $venda['itens'] = $stmtItens->fetchAll();
        return $venda;
    }

    /** Indicadores simples para o painel inicial. */
    public function resumo(): array
    {
        return $this->pdo->query(
            'SELECT COUNT(*) AS num_vendas,
                    COALESCE(SUM(total), 0) AS faturacao,
                    COALESCE(SUM(CASE WHEN DATE(data_venda) = CURDATE() THEN total END), 0) AS faturacao_hoje
               FROM vendas'
        )->fetch();
    }
}
