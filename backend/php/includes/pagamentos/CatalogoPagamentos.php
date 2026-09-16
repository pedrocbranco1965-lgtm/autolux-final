<?php
/**
 * Catálogo de pagamentos apresentado na página PHP.
 *
 * EXTENSIBILIDADE: para acrescentar um método novo:
 *   1. Criar uma classe em includes/pagamentos/metodos/ que implemente MetodoPagamento
 *   2. Fazer require_once no bootstrap.php
 *   3. Adicionar `new OMeuMetodo()` ao array abaixo
 *
 * A página de venda não precisa de mais alterações — percorre este catálogo.
 */
final class CatalogoPagamentos
{
    /** @return MetodoPagamento[] */
    public static function todos(): array
    {
        return [
            new Numerario(),
            new Multibanco(),
            new MbWay(),
            new Transferencia(),
            new Cartao(),
        ];
    }

    public static function obter(string $codigo): ?MetodoPagamento
    {
        foreach (self::todos() as $metodo) {
            if ($metodo->codigo() === $codigo) {
                return $metodo;
            }
        }
        return null;
    }
}
