<?php

declare(strict_types=1);

namespace App\Support;

/** Token anti-CSRF incluído em todos os formulários que alteram dados. */
final class Csrf
{
    private const CHAVE = '_csrf_token';

    public static function token(): string
    {
        if (empty($_SESSION[self::CHAVE])) {
            $_SESSION[self::CHAVE] = bin2hex(random_bytes(32));
        }

        return $_SESSION[self::CHAVE];
    }

    public static function campo(): string
    {
        return sprintf('<input type="hidden" name="_token" value="%s">', htmlspecialchars(self::token(), ENT_QUOTES));
    }

    public static function valido(?string $token): bool
    {
        return is_string($token) && hash_equals(self::token(), $token);
    }
}
