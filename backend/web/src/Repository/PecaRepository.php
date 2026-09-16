<?php

declare(strict_types=1);

namespace App\Repository;

use App\Support\Database;
use PDO;

/** Acesso ao catálogo de material (tabela pecas da Base de Dados 1). */
final class PecaRepository
{
    private PDO $pdo;

    public function __construct()
    {
        $this->pdo = Database::ligacao();
    }

    /**
     * Pesquisa peças aplicando os filtros do catálogo.
     * Cada filtro acrescenta uma condição e um parâmetro ligado, o que evita
     * concatenar valores no SQL e mantém a consulta imune a SQL injection.
     *
     * @param array{marca_id?: int|null, tipo_id?: int|null, preco_min?: float|null,
     *              preco_max?: float|null, pesquisa?: string|null, ordem?: string|null} $filtros
     * @return array<int, array<string, mixed>>
     */
    public function pesquisar(array $filtros = []): array
    {
        $condicoes = ['ativo = 1'];
        $parametros = [];

        if (!empty($filtros['marca_id'])) {
            $condicoes[] = 'marca_id = :marca_id';
            $parametros['marca_id'] = (int) $filtros['marca_id'];
        }
        if (!empty($filtros['tipo_id'])) {
            $condicoes[] = 'tipo_id = :tipo_id';
            $parametros['tipo_id'] = (int) $filtros['tipo_id'];
        }
        if (isset($filtros['preco_min']) && $filtros['preco_min'] !== null) {
            $condicoes[] = 'preco >= :preco_min';
            $parametros['preco_min'] = (float) $filtros['preco_min'];
        }
        if (isset($filtros['preco_max']) && $filtros['preco_max'] !== null) {
            $condicoes[] = 'preco <= :preco_max';
            $parametros['preco_max'] = (float) $filtros['preco_max'];
        }
        if (!empty($filtros['pesquisa'])) {
            $condicoes[] = '(designacao LIKE :pesquisa OR referencia LIKE :pesquisa)';
            $parametros['pesquisa'] = '%' . $filtros['pesquisa'] . '%';
        }
        if (!empty($filtros['apenas_stock'])) {
            $condicoes[] = 'stock > 0';
        }

        $sql = 'SELECT * FROM vw_catalogo_pecas WHERE ' . implode(' AND ', $condicoes)
            . ' ORDER BY ' . $this->ordenacao($filtros['ordem'] ?? null);

        $consulta = $this->pdo->prepare($sql);
        $consulta->execute($parametros);

        return $consulta->fetchAll();
    }

    /** @return array<string, mixed>|null */
    public function procurarPorId(int $id): ?array
    {
        $consulta = $this->pdo->prepare('SELECT * FROM vw_catalogo_pecas WHERE id = :id');
        $consulta->execute(['id' => $id]);

        return $consulta->fetch() ?: null;
    }

    /** @return array<int, array<string, mixed>> */
    public function listarParaVenda(): array
    {
        return $this->pdo
            ->query('SELECT * FROM vw_catalogo_pecas WHERE ativo = 1 AND stock > 0 ORDER BY designacao')
            ->fetchAll();
    }

    /** @return array<int, array{id: int, nome: string}> */
    public function marcas(): array
    {
        return $this->pdo->query('SELECT id, nome FROM marcas ORDER BY nome')->fetchAll();
    }

    /** @return array<int, array{id: int, nome: string}> */
    public function tipos(): array
    {
        return $this->pdo->query('SELECT id, nome FROM tipos_peca ORDER BY nome')->fetchAll();
    }

    /** @return array{minimo: float, maximo: float} */
    public function intervaloPrecos(): array
    {
        $linha = $this->pdo
            ->query('SELECT MIN(preco) AS minimo, MAX(preco) AS maximo FROM pecas WHERE ativo = 1')
            ->fetch();

        return [
            'minimo' => (float) ($linha['minimo'] ?? 0),
            'maximo' => (float) ($linha['maximo'] ?? 0),
        ];
    }

    /** Peças cujo stock já está no limite mínimo definido para reposição. */
    public function emRutura(): array
    {
        return $this->pdo
            ->query(
                'SELECT * FROM vw_catalogo_pecas
                 WHERE ativo = 1 AND stock <= stock_minimo
                 ORDER BY stock ASC, designacao',
            )
            ->fetchAll();
    }

    public function totalPecas(): int
    {
        return (int) $this->pdo->query('SELECT COUNT(*) FROM pecas WHERE ativo = 1')->fetchColumn();
    }

    public function valorStock(): float
    {
        return (float) $this->pdo
            ->query('SELECT COALESCE(SUM(preco * stock), 0) FROM pecas WHERE ativo = 1')
            ->fetchColumn();
    }

    private function ordenacao(?string $ordem): string
    {
        // Lista branca: o utilizador escolhe a chave, nunca o SQL.
        return match ($ordem) {
            'preco_asc' => 'preco ASC',
            'preco_desc' => 'preco DESC',
            'stock_asc' => 'stock ASC',
            'marca' => 'marca ASC, designacao ASC',
            default => 'designacao ASC',
        };
    }
}
