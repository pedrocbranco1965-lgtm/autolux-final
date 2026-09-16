<?php

declare(strict_types=1);

namespace App\Support;

use RuntimeException;

/**
 * Renderiza as páginas dinâmicas: cada página PHP prepara os dados e delega a
 * apresentação numa vista, mantendo o HTML separado da lógica de negócio.
 */
final class View
{
    /** @param array<string, mixed> $dados */
    public static function render(string $vista, array $dados = []): void
    {
        $caminho = self::caminho($vista);

        extract($dados, EXTR_SKIP);
        $paginaAtiva = $dados['paginaAtiva'] ?? $vista;
        $titulo = $dados['titulo'] ?? 'AutoLux';

        require self::caminho('layout/header');
        require $caminho;
        require self::caminho('layout/footer');
    }

    /** Inclui um bloco reutilizável (sem cabeçalho nem rodapé). */
    public static function partial(string $vista, array $dados = []): void
    {
        extract($dados, EXTR_SKIP);
        require self::caminho($vista);
    }

    private static function caminho(string $vista): string
    {
        $caminho = dirname(__DIR__, 2) . '/views/' . $vista . '.php';
        if (!is_file($caminho)) {
            throw new RuntimeException("Vista não encontrada: {$vista}");
        }

        return $caminho;
    }
}
