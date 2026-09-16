<?php

declare(strict_types=1);

namespace App\Support;

/** Formatação de valores para apresentação (moeda, datas e estados). */
final class Format
{
    public static function moeda(float|string|null $valor): string
    {
        return number_format((float) $valor, 2, ',', ' ') . ' €';
    }

    public static function data(?string $dataHora, string $formato = 'd/m/Y'): string
    {
        if ($dataHora === null || $dataHora === '') {
            return '—';
        }

        $data = date_create($dataHora);

        return $data === false ? '—' : $data->format($formato);
    }

    public static function dataHora(?string $dataHora): string
    {
        return self::data($dataHora, 'd/m/Y H:i');
    }

    /** Classe CSS associada ao estado de uma venda ou encomenda. */
    public static function classeEstado(string $estado): string
    {
        return match ($estado) {
            'paga', 'recebida' => 'badge badge--ok',
            'pendente', 'submetida' => 'badge badge--aviso',
            'confirmada' => 'badge badge--info',
            'anulada', 'cancelada' => 'badge badge--erro',
            default => 'badge',
        };
    }
}
