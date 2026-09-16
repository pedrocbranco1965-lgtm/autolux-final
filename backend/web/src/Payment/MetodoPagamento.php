<?php

declare(strict_types=1);

namespace App\Payment;

/**
 * Contrato de um método de pagamento.
 *
 * Cada método decide (1) que campos extra mostra no formulário, (2) como valida
 * esses campos e (3) que referência/estado gera para a venda. Introduzir um novo
 * método de pagamento resume-se a criar uma classe que implemente este contrato
 * e registá-la em RegistoMetodosPagamento — nenhuma página PHP precisa de mudar.
 */
interface MetodoPagamento
{
    /** Código igual ao da coluna metodos_pagamento.codigo. */
    public function codigo(): string;

    /**
     * Campos adicionais pedidos ao funcionário no formulário de encomenda.
     *
     * @return array<int, array{nome: string, etiqueta: string, tipo: string,
     *                          exemplo?: string, obrigatorio: bool, ajuda?: string}>
     */
    public function campos(): array;

    /**
     * Valida os dados introduzidos para este método.
     *
     * @param array<string, string> $dados
     * @return array<int, string> Lista de erros; vazia quando está tudo correto.
     */
    public function validar(array $dados): array;

    /**
     * Processa o pagamento da venda e devolve o estado com que esta fica
     * registada, juntamente com a referência a apresentar ao cliente.
     *
     * @param array<string, string> $dados
     */
    public function processar(array $dados, float $total): ResultadoPagamento;
}
