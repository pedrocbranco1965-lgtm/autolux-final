<?php
declare(strict_types=1);

namespace AutoLux\Pagamentos;

/**
 * Contrato que todos os métodos de pagamento têm de cumprir.
 *
 * Para adicionar um novo tipo de pagamento basta:
 *   1. Criar uma classe nesta pasta que implemente esta interface;
 *   2. Registá-la em php/config/pagamentos.php.
 * Não é necessário alterar o formulário nem a base de dados.
 */
interface MetodoPagamento
{
    /** Código curto e único guardado na coluna vendas.tipo_pagamento (ex.: "mbway"). */
    public function codigo(): string;

    /** Nome apresentado ao utilizador no formulário. */
    public function nome(): string;

    /** Texto de ajuda mostrado por baixo da opção. */
    public function descricao(): string;

    /**
     * Indica se este método precisa de um campo extra (nº de telemóvel,
     * referência, últimos dígitos do cartão...). Devolve null se não precisar.
     */
    public function etiquetaCampoExtra(): ?string;

    /**
     * Valida o valor do campo extra. Devolve uma mensagem de erro ou null se estiver OK.
     */
    public function validarCampoExtra(?string $valor): ?string;

    /**
     * Devolve o texto a guardar em vendas.detalhe_pagamento (ex.: referência gerada).
     * Recebe o total da venda para métodos que geram referências.
     */
    public function detalhe(float $total, ?string $valorExtra): ?string;
}
