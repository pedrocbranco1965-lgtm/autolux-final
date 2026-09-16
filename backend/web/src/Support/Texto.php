<?php

declare(strict_types=1);

namespace App\Support;

/** Utilitários de texto que funcionam com ou sem a extensão mbstring instalada. */
final class Texto
{
    public static function limitar(string $texto, int $maximo): string
    {
        if (function_exists('mb_substr')) {
            return mb_substr($texto, 0, $maximo);
        }

        return substr($texto, 0, $maximo);
    }
}
