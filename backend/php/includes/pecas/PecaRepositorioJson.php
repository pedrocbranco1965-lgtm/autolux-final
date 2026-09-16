<?php
/**
 * Fonte A — catálogo lido de um ficheiro JSON local.
 */
final class PecaRepositorioJson implements PecaRepositorioInterface
{
    public function __construct(private string $caminho)
    {
    }

    public function listar(?string $marca, ?string $tipo, ?float $precoMin, ?float $precoMax): array
    {
        return $this->filtrar($this->todas(), $marca, $tipo, $precoMin, $precoMax);
    }

    public function obter(int $id): ?array
    {
        foreach ($this->todas() as $peca) {
            if ((int) $peca['id'] === $id) {
                return $peca;
            }
        }
        return null;
    }

    public function marcas(): array
    {
        $valores = array_unique(array_map(fn ($p) => $p['marca'], $this->todas()));
        sort($valores);
        return array_values($valores);
    }

    public function tipos(): array
    {
        $valores = array_unique(array_map(fn ($p) => $p['tipo'], $this->todas()));
        sort($valores);
        return array_values($valores);
    }

    private function todas(): array
    {
        if (!is_file($this->caminho)) {
            throw new RuntimeException('Ficheiro JSON de peças não encontrado: ' . $this->caminho);
        }
        $dados = json_decode((string) file_get_contents($this->caminho), true);
        if (!is_array($dados)) {
            throw new RuntimeException('JSON de peças inválido.');
        }
        return $dados;
    }

    private function filtrar(array $pecas, ?string $marca, ?string $tipo, ?float $precoMin, ?float $precoMax): array
    {
        return array_values(array_filter($pecas, function (array $peca) use ($marca, $tipo, $precoMin, $precoMax) {
            if ($marca && $peca['marca'] !== $marca) {
                return false;
            }
            if ($tipo && $peca['tipo'] !== $tipo) {
                return false;
            }
            $preco = (float) $peca['preco'];
            if ($precoMin !== null && $preco < $precoMin) {
                return false;
            }
            if ($precoMax !== null && $preco > $precoMax) {
                return false;
            }
            return true;
        }));
    }
}
