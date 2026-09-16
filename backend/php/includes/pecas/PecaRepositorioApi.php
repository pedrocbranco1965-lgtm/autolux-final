<?php
/**
 * Fonte B — catálogo obtido por HTTP a partir de um endpoint REST
 * (php/api_pecas.php), que por sua vez lê o MySQL.
 * Serve para demonstrar uma versão do trabalho alimentada por API.
 */
final class PecaRepositorioApi implements PecaRepositorioInterface
{
    public function __construct(private string $urlBase)
    {
    }

    public function listar(?string $marca, ?string $tipo, ?float $precoMin, ?float $precoMax): array
    {
        $query = http_build_query(array_filter([
            'marca' => $marca,
            'tipo' => $tipo,
            'preco_min' => $precoMin,
            'preco_max' => $precoMax,
        ], fn ($v) => $v !== null && $v !== ''));

        $url = $this->urlBase . ($query ? ('?' . $query) : '');
        $dados = http_get_json($url);
        return $dados['pecas'] ?? [];
    }

    public function obter(int $id): ?array
    {
        $dados = http_get_json($this->urlBase . '?id=' . $id);
        return $dados['peca'] ?? null;
    }

    public function marcas(): array
    {
        $dados = http_get_json($this->urlBase . '?meta=1');
        return $dados['marcas'] ?? [];
    }

    public function tipos(): array
    {
        $dados = http_get_json($this->urlBase . '?meta=1');
        return $dados['tipos'] ?? [];
    }
}
