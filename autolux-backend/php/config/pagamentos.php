<?php
/**
 * Métodos de pagamento disponíveis no formulário de venda.
 *
 * EXTENSIBILIDADE: para introduzir um novo método (ex.: PayPal), cria-se a
 * classe AutoLux\Pagamentos\PayPal que implementa MetodoPagamento e
 * acrescenta-se uma linha a esta lista. A ordem aqui é a ordem na página.
 */
declare(strict_types=1);

use AutoLux\Pagamentos;

return [
    Pagamentos\Dinheiro::class,
    Pagamentos\Multibanco::class,
    Pagamentos\MBWay::class,
    Pagamentos\CartaoCredito::class,
    Pagamentos\Transferencia::class,
];
