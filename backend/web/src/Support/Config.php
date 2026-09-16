<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Lê a configuração do ficheiro .env partilhado com o serviço Node.js.
 * Se o .env não existir, recorre ao .env.example, para que o projeto arranque
 * imediatamente numa instalação normal (XAMPP/WAMP).
 */
final class Config
{
    /** @var array<string, string>|null */
    private static ?array $valores = null;

    public static function get(string $chave, ?string $omissao = null): ?string
    {
        self::carregar();

        return self::$valores[$chave] ?? $omissao;
    }

    public static function inteiro(string $chave, int $omissao): int
    {
        $valor = self::get($chave);

        return $valor === null || $valor === '' ? $omissao : (int) $valor;
    }

    /** @return array<string, string> */
    public static function todos(): array
    {
        self::carregar();

        return self::$valores;
    }

    private static function carregar(): void
    {
        if (self::$valores !== null) {
            return;
        }

        self::$valores = [];
        $raiz = dirname(__DIR__, 3);

        // O .env.example é lido primeiro para servir de valores por omissão.
        foreach (['.env.example', '.env'] as $ficheiro) {
            $caminho = $raiz . DIRECTORY_SEPARATOR . $ficheiro;
            if (is_readable($caminho)) {
                self::$valores = array_merge(self::$valores, self::analisar($caminho));
            }
        }
    }

    /** @return array<string, string> */
    private static function analisar(string $caminho): array
    {
        $valores = [];
        $linhas = file($caminho, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES) ?: [];

        foreach ($linhas as $linha) {
            $linha = trim($linha);
            if ($linha === '' || str_starts_with($linha, '#') || !str_contains($linha, '=')) {
                continue;
            }

            [$chave, $valor] = explode('=', $linha, 2);
            $valores[trim($chave)] = trim($valor, " \t\"'");
        }

        return $valores;
    }
}
