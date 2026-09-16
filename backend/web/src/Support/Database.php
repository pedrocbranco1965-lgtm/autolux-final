<?php

declare(strict_types=1);

namespace App\Support;

use PDO;
use PDOException;
use RuntimeException;

/**
 * Ponto único de acesso à Base de Dados 1 (clientes, material e vendas).
 * A ligação PDO é criada uma só vez por pedido e reutilizada pelos repositórios.
 */
final class Database
{
    private static ?PDO $ligacao = null;

    private function __construct()
    {
    }

    public static function ligacao(): PDO
    {
        if (self::$ligacao instanceof PDO) {
            return self::$ligacao;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%d;dbname=%s;charset=utf8mb4',
            Config::get('DB_HOST', '127.0.0.1'),
            Config::inteiro('DB_PORT', 3306),
            Config::get('DB_VENDAS', 'autolux_vendas'),
        );

        try {
            self::$ligacao = new PDO(
                $dsn,
                Config::get('DB_USER', 'root'),
                Config::get('DB_PASSWORD', ''),
                [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                    PDO::ATTR_STRINGIFY_FETCHES => false,
                ],
            );
        } catch (PDOException $erro) {
            throw new RuntimeException(
                'Não foi possível ligar à base de dados de vendas. '
                . 'Verifique se o MySQL está a correr e se já executou "npm run db:setup". '
                . 'Detalhe: ' . $erro->getMessage(),
                (int) $erro->getCode(),
                $erro,
            );
        }

        return self::$ligacao;
    }

    /** Executa um conjunto de operações dentro de uma transação. */
    public static function transacao(callable $operacoes): mixed
    {
        $pdo = self::ligacao();
        $pdo->beginTransaction();

        try {
            $resultado = $operacoes($pdo);
            $pdo->commit();

            return $resultado;
        } catch (\Throwable $erro) {
            $pdo->rollBack();

            throw $erro;
        }
    }
}
