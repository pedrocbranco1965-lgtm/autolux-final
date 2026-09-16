<?php
/**
 * Fonte C — leitura direta da Base de Dados 1 (MySQL).
 * É a versão pedida no mapeamento técnico do enunciado.
 */
final class PecaRepositorioMysql implements PecaRepositorioInterface
{
    public function __construct(private PDO $pdo)
    {
    }

    public function listar(?string $marca, ?string $tipo, ?float $precoMin, ?float $precoMax): array
    {
        $sql = 'SELECT * FROM pecas WHERE 1=1';
        $params = [];

        if ($marca) {
            $sql .= ' AND marca = :marca';
            $params['marca'] = $marca;
        }
        if ($tipo) {
            $sql .= ' AND tipo = :tipo';
            $params['tipo'] = $tipo;
        }
        if ($precoMin !== null) {
            $sql .= ' AND preco >= :pmin';
            $params['pmin'] = $precoMin;
        }
        if ($precoMax !== null) {
            $sql .= ' AND preco <= :pmax';
            $params['pmax'] = $precoMax;
        }

        $sql .= ' ORDER BY marca, nome';
        $stmt = $this->pdo->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }

    public function obter(int $id): ?array
    {
        $stmt = $this->pdo->prepare('SELECT * FROM pecas WHERE id = :id');
        $stmt->execute(['id' => $id]);
        $linha = $stmt->fetch();
        return $linha ?: null;
    }

    public function marcas(): array
    {
        return $this->pdo->query('SELECT DISTINCT marca FROM pecas ORDER BY marca')->fetchAll(PDO::FETCH_COLUMN);
    }

    public function tipos(): array
    {
        return $this->pdo->query('SELECT DISTINCT tipo FROM pecas ORDER BY tipo')->fetchAll(PDO::FETCH_COLUMN);
    }
}
