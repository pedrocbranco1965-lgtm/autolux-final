<?php
/**
 * Contrato comum das três fontes de dados do catálogo.
 * As páginas PHP falam só com esta interface — não sabem se os dados
 * vêm de JSON, de uma API ou do MySQL.
 */
interface PecaRepositorioInterface
{
    public function listar(?string $marca, ?string $tipo, ?float $precoMin, ?float $precoMax): array;

    public function obter(int $id): ?array;

    public function marcas(): array;

    public function tipos(): array;
}
