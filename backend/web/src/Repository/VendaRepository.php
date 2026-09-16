<?php

declare(strict_types=1);

namespace App\Repository;

use App\Support\Database;
use PDO;

/** Registo e consulta de vendas (encomendas de peças para cliente). */
final class VendaRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::ligacao();
    }

    /** @return array<int, array<string, mixed>> */
    public function listar(?int $clienteId = null): array
    {
        $sql = 'SELECT v.*,
                       c.nome AS cliente,
                       m.designacao AS metodo_pagamento,
                       (SELECT COALESCE(SUM(i.quantidade), 0) FROM venda_itens i WHERE i.venda_id = v.id) AS total_artigos
                FROM vendas v
                         INNER JOIN clientes c ON c.id = v.cliente_id
                         INNER JOIN metodos_pagamento m ON m.id = v.metodo_pagamento_id';

        $parametros = [];
        if ($clienteId !== null) {
            $sql .= ' WHERE v.cliente_id = :cliente_id';
            $parametros['cliente_id'] = $clienteId;
        }

        $consulta = $this->pdo->prepare($sql . ' ORDER BY v.criada_em DESC, v.id DESC');
        $consulta->execute($parametros);

        return $consulta->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public function procurarPorId(int $id): ?array
    {
        $consulta = $this->pdo->prepare(
            'SELECT v.*, c.nome AS cliente, c.email AS cliente_email, m.designacao AS metodo_pagamento, m.codigo AS metodo_codigo
             FROM vendas v
                      INNER JOIN clientes c ON c.id = v.cliente_id
                      INNER JOIN metodos_pagamento m ON m.id = v.metodo_pagamento_id
             WHERE v.id = :id',
        );
        $consulta->execute(['id' => $id]);
        $venda = $consulta->fetch();

        if ($venda === false) {
            return null;
        }

        $itens = $this->pdo->prepare(
            'SELECT i.*, p.referencia, p.designacao
             FROM venda_itens i
                      INNER JOIN pecas p ON p.id = i.peca_id
             WHERE i.venda_id = :id
             ORDER BY i.id',
        );
        $itens->execute(['id' => $id]);
        $venda['itens'] = $itens->fetchAll();

        return $venda;
    }

    /**
     * Grava a venda, as respetivas linhas e abate o stock, tudo na mesma
     * transação. O stock só é abatido se continuar disponível no momento da
     * gravação (condição stock >= quantidade), o que impede stock negativo
     * quando duas vendas da mesma peça ocorrem em simultâneo.
     *
     * @param array<int, array{peca_id: int, quantidade: int, preco_unitario: float}> $itens
     */
    public function registar(
        int $clienteId,
        int $metodoPagamentoId,
        ?string $referenciaPagamento,
        string $estado,
        ?string $observacoes,
        array $itens,
    ): int {
        return Database::transacao(function (PDO $pdo) use (
            $clienteId,
            $metodoPagamentoId,
            $referenciaPagamento,
            $estado,
            $observacoes,
            $itens,
        ): int {
            $total = 0.0;
            foreach ($itens as $item) {
                $total += $item['quantidade'] * $item['preco_unitario'];
            }

            $cabecalho = $pdo->prepare(
                'INSERT INTO vendas (numero, cliente_id, metodo_pagamento_id, referencia_pagamento, estado, total, observacoes)
                 VALUES (:numero, :cliente_id, :metodo_pagamento_id, :referencia_pagamento, :estado, :total, :observacoes)',
            );
            $cabecalho->execute([
                'numero' => $this->gerarNumero($pdo),
                'cliente_id' => $clienteId,
                'metodo_pagamento_id' => $metodoPagamentoId,
                'referencia_pagamento' => $referenciaPagamento,
                'estado' => $estado,
                'total' => round($total, 2),
                'observacoes' => $observacoes,
            ]);

            $vendaId = (int) $pdo->lastInsertId();

            $linha = $pdo->prepare(
                'INSERT INTO venda_itens (venda_id, peca_id, quantidade, preco_unitario, subtotal)
                 VALUES (:venda_id, :peca_id, :quantidade, :preco_unitario, :subtotal)',
            );
            // Os dois parâmetros da quantidade são distintos porque o PDO, com
            // prepared statements nativos, não permite repetir o mesmo nome.
            $abate = $pdo->prepare(
                'UPDATE pecas SET stock = stock - :quantidade
                 WHERE id = :peca_id AND stock >= :quantidade_minima',
            );

            foreach ($itens as $item) {
                $linha->execute([
                    'venda_id' => $vendaId,
                    'peca_id' => $item['peca_id'],
                    'quantidade' => $item['quantidade'],
                    'preco_unitario' => $item['preco_unitario'],
                    'subtotal' => round($item['quantidade'] * $item['preco_unitario'], 2),
                ]);

                $abate->execute([
                    'quantidade' => $item['quantidade'],
                    'quantidade_minima' => $item['quantidade'],
                    'peca_id' => $item['peca_id'],
                ]);
                if ($abate->rowCount() === 0) {
                    throw new \RuntimeException('Stock insuficiente para concluir a encomenda.');
                }
            }

            return $vendaId;
        });
    }

    public function totalVendasMes(): float
    {
        return (float) $this->pdo->query(
            'SELECT COALESCE(SUM(total), 0) FROM vendas
             WHERE estado <> "anulada"
               AND YEAR(criada_em) = YEAR(CURDATE())
               AND MONTH(criada_em) = MONTH(CURDATE())',
        )->fetchColumn();
    }

    public function totalVendas(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM vendas')->fetchColumn();
    }

    /** @return array<int, array<string, mixed>> */
    public function ultimas(int $limite = 5): array
    {
        $consulta = $this->pdo->prepare(
            'SELECT v.numero, v.total, v.estado, v.criada_em, c.nome AS cliente
             FROM vendas v
                      INNER JOIN clientes c ON c.id = v.cliente_id
             ORDER BY v.criada_em DESC, v.id DESC
             LIMIT :limite',
        );
        $consulta->bindValue('limite', $limite, PDO::PARAM_INT);
        $consulta->execute();

        return $consulta->fetchAll();
    }

    /** Numeração sequencial por ano: V2026-0001, V2026-0002, ... */
    private function gerarNumero(PDO $pdo): string
    {
        $ano = (int) date('Y');
        $consulta = $pdo->prepare(
            'SELECT COALESCE(MAX(CAST(SUBSTRING(numero, 7) AS UNSIGNED)), 0)
             FROM vendas WHERE numero LIKE :prefixo FOR UPDATE',
        );
        $consulta->execute(['prefixo' => "V{$ano}-%"]);

        return sprintf('V%d-%04d', $ano, ((int) $consulta->fetchColumn()) + 1);
    }
}
