<?php

declare(strict_types=1);

namespace App\Service;

use App\Repository\PecaRepository;

/** Lógica do catálogo de peças: normaliza os filtros recebidos do formulário. */
final class CatalogoService
{
    public function __construct(private PecaRepository $pecas = new PecaRepository())
    {
    }

    /**
     * Converte os parâmetros do GET em filtros válidos, ignorando valores vazios
     * e corrigindo intervalos de preço invertidos.
     *
     * @param array<string, mixed> $parametros
     * @return array<string, mixed>
     */
    public function normalizarFiltros(array $parametros): array
    {
        $numero = static function (mixed $valor): ?float {
            $valor = is_string($valor) ? str_replace(',', '.', trim($valor)) : $valor;

            return is_numeric($valor) ? (float) $valor : null;
        };

        $precoMin = $numero($parametros['preco_min'] ?? null);
        $precoMax = $numero($parametros['preco_max'] ?? null);

        if ($precoMin !== null && $precoMax !== null && $precoMin > $precoMax) {
            [$precoMin, $precoMax] = [$precoMax, $precoMin];
        }

        return [
            'marca_id' => isset($parametros['marca_id']) && $parametros['marca_id'] !== ''
                ? (int) $parametros['marca_id'] : null,
            'tipo_id' => isset($parametros['tipo_id']) && $parametros['tipo_id'] !== ''
                ? (int) $parametros['tipo_id'] : null,
            'preco_min' => $precoMin,
            'preco_max' => $precoMax,
            'pesquisa' => isset($parametros['pesquisa']) ? trim((string) $parametros['pesquisa']) : '',
            'ordem' => isset($parametros['ordem']) ? (string) $parametros['ordem'] : 'designacao',
            'apenas_stock' => !empty($parametros['apenas_stock']),
        ];
    }

    /**
     * @param array<string, mixed> $filtros
     * @return array<int, array<string, mixed>>
     */
    public function pesquisar(array $filtros): array
    {
        return $this->pecas->pesquisar($filtros);
    }

    /** @return array{marcas: array, tipos: array, precos: array{minimo: float, maximo: float}} */
    public function opcoesDeFiltro(): array
    {
        return [
            'marcas' => $this->pecas->marcas(),
            'tipos' => $this->pecas->tipos(),
            'precos' => $this->pecas->intervaloPrecos(),
        ];
    }

    /** @param array<string, mixed> $filtros */
    public function temFiltrosAtivos(array $filtros): bool
    {
        return $filtros['marca_id'] !== null
            || $filtros['tipo_id'] !== null
            || $filtros['preco_min'] !== null
            || $filtros['preco_max'] !== null
            || $filtros['pesquisa'] !== ''
            || $filtros['apenas_stock'];
    }

    /** @param array<int, array<string, mixed>> $pecas */
    public function valorTotal(array $pecas): float
    {
        return array_sum(array_map(static fn (array $peca): float => (float) $peca['preco'], $pecas));
    }
}
