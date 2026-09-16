<?php

declare(strict_types=1);

namespace App\Support;

/**
 * Mensagens de sucesso/erro guardadas em sessão entre o POST e o redirecionamento
 * seguinte (padrão Post/Redirect/Get), evitando reenvios do formulário com F5.
 */
final class Flash
{
    private const CHAVE = '_flash';

    public static function sucesso(string $mensagem): void
    {
        self::adicionar('sucesso', $mensagem);
    }

    public static function erro(string $mensagem): void
    {
        self::adicionar('erro', $mensagem);
    }

    /** @return array<int, array{tipo: string, mensagem: string}> */
    public static function consumir(): array
    {
        $mensagens = $_SESSION[self::CHAVE] ?? [];
        unset($_SESSION[self::CHAVE]);

        return $mensagens;
    }

    private static function adicionar(string $tipo, string $mensagem): void
    {
        $_SESSION[self::CHAVE][] = ['tipo' => $tipo, 'mensagem' => $mensagem];
    }
}
