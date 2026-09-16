<?php
interface MetodoPagamento
{
    public function codigo(): string;

    public function nome(): string;

    public function descricao(): string;

    /**
     * Devolve um texto de confirmação que fica guardado na venda.
     * Cada método pode gerar referências, prazos, etc.
     */
    public function confirmar(array $venda): string;
}
