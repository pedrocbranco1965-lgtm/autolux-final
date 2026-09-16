<?php
final class PecaFactory
{
    public static function criar(): PecaRepositorioInterface
    {
        return match (FONTE_DADOS) {
            'A' => new PecaRepositorioJson(JSON_PECAS),
            'B' => new PecaRepositorioApi(APP_URL . '/api_pecas.php'),
            default => new PecaRepositorioMysql(Database::vendas()),
        };
    }
}
