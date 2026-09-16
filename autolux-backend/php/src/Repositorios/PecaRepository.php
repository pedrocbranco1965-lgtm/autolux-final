<?php
declare(strict_types=1);

namespace AutoLux\Repositorios;

use AutoLux\Database;
use PDO;

/** Acesso à tabela `pecas` (catálogo / stock). */
final class PecaRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::ligacao();
    }

    /**
     * Lista peças aplicando os filtros do catálogo.
     * Todos os filtros são opcionais; os valores vão sempre por parâmetros
     * (prepared statements) para evitar SQL injection.
     *
     * @param array{marca_id?:string, tipo_id?:string, preco_min?:string, preco_max?:string, pesquisa?:string, so_stock_baixo?:string} $filtros
     */
    public function listar(array $filtros = []): array
    {
        $sql = 'SELECT p.*, m.nome AS marca, t.nome AS tipo
                  FROM pecas p
                  JOIN marcas m ON m.id = p.marca_id
                  JOIN tipos_peca t ON t.id = p.tipo_id';
        $condicoes = [];
        $params = [];

        if (!empty($filtros['marca_id'])) {
            $condicoes[] = 'p.marca_id = :marca_id';
            $params['marca_id'] = (int) $filtros['marca_id'];
        }
        if (!empty($filtros['tipo_id'])) {
            $condicoes[] = 'p.tipo_id = :tipo_id';
            $params['tipo_id'] = (int) $filtros['tipo_id'];
        }
        if (isset($filtros['preco_min']) && $filtros['preco_min'] !== '') {
            $condicoes[] = 'p.preco >= :preco_min';
            $params['preco_min'] = (float) $filtros['preco_min'];
        }
        if (isset($filtros['preco_max']) && $filtros['preco_max'] !== '') {
            $condicoes[] = 'p.preco <= :preco_max';
            $params['preco_max'] = (float) $filtros['preco_max'];
        }
        if (!empty($filtros['pesquisa'])) {
            $condicoes[] = '(p.nome LIKE :pesquisa OR p.referencia LIKE :pesquisa OR p.descricao LIKE :pesquisa)';
            $params['pesquisa'] = '%' . $filtros['pesquisa'] . '%';
        }
        if (!empty($filtros['so_stock_baixo'])) {
            $condicoes[] = 'p.stock <= p.stock_minimo';
        }

        if ($condicoes) {
            $sql .= ' WHERE ' . implode(' AND ', $condicoes);
        }
        $sql .= ' ORDER BY m.nome, p.nome';

        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function obter(int $id): ?array
    {
        $stmt = $this->pdo->prepare(
            'SELECT p.*, m.nome AS marca, t.nome AS tipo
               FROM pecas p
               JOIN marcas m ON m.id = p.marca_id
               JOIN tipos_peca t ON t.id = p.tipo_id
              WHERE p.id = :id'
        );
        $stmt->execute(['id' => $id]);
        return $stmt->fetch() ?: null;
    }

    public function marcas(): array
    {
        return $this->pdo->query('SELECT id, nome FROM marcas ORDER BY nome')->fetchAll();
    }

    public function tiposPeca(): array
    {
        return $this->pdo->query('SELECT id, nome FROM tipos_peca ORDER BY nome')->fetchAll();
    }

    /** Intervalo de preços do catálogo, usado para sugerir limites nos filtros. */
    public function intervaloPrecos(): array
    {
        return $this->pdo->query('SELECT MIN(preco) AS minimo, MAX(preco) AS maximo FROM pecas')->fetch();
    }

    public function contarStockBaixo(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM pecas WHERE stock <= stock_minimo')->fetchColumn();
    }
}
