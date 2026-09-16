<?php
declare(strict_types=1);

namespace AutoLux;

use PDO;
use PDOException;

/**
 * Ligação única (singleton) à Base de Dados 1 através de PDO.
 * Todos os repositórios pedem a ligação aqui: Database::ligacao().
 */
final class Database
{
    private static ?PDO $pdo = null;

    public static function ligacao(): PDO
    {
        if (self::$pdo === null) {
            $cfg = $GLOBALS['config'];
            $dsn = sprintf(
                'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
                $cfg['DB_HOST'],
                $cfg['DB_PORT'],
                $cfg['DB1_NAME']
            );

            try {
                self::$pdo = new PDO($dsn, $cfg['DB_USER'], $cfg['DB_PASSWORD'], [
                    PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION, // erros SQL lançam exceções
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,       // linhas como arrays associativos
                    PDO::ATTR_EMULATE_PREPARES   => false,                  // prepared statements reais (segurança)
                ]);
            } catch (PDOException $e) {
                throw new \RuntimeException(
                    'Não foi possível ligar à base de dados "' . $cfg['DB1_NAME'] . '". ' .
                    'Confirme que o MySQL está a correr, que executou "npm run db:setup" e que o ficheiro .env está correto. ' .
                    'Detalhe: ' . $e->getMessage()
                );
            }
        }

        return self::$pdo;
    }
}
