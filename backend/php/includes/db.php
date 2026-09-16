<?php
final class Database
{
    private static ?PDO $vendas = null;

    public static function vendas(): PDO
    {
        if (self::$vendas instanceof PDO) {
            return self::$vendas;
        }

        $dsn = sprintf(
            'mysql:host=%s;port=%s;dbname=%s;charset=utf8mb4',
            DB1_HOST,
            DB1_PORT,
            DB1_NAME
        );

        $ultimoErro = null;
        for ($i = 1; $i <= 8; $i++) {
            try {
                self::$vendas = new PDO($dsn, DB1_USER, DB1_PASS, [
                    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                    PDO::ATTR_EMULATE_PREPARES => false,
                ]);
                return self::$vendas;
            } catch (PDOException $erro) {
                $ultimoErro = $erro;
                sleep(1);
            }
        }

        throw new RuntimeException(
            'Não foi possível ligar à Base de Dados 1 (vendas): ' . ($ultimoErro?->getMessage() ?? 'erro desconhecido')
        );
    }
}
