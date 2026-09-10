<?php

namespace App\Support;

/**
 * Conversor sencillo de números a su representación en letras (español).
 * Cubre montos hasta millones con hasta 2 decimales, suficiente para
 * los comprobantes de anticipo.
 */
class NumberToWords
{
    private const UNIDADES = [
        0 => 'cero', 1 => 'uno', 2 => 'dos', 3 => 'tres', 4 => 'cuatro',
        5 => 'cinco', 6 => 'seis', 7 => 'siete', 8 => 'ocho', 9 => 'nueve',
        10 => 'diez', 11 => 'once', 12 => 'doce', 13 => 'trece', 14 => 'catorce',
        15 => 'quince', 16 => 'dieciséis', 17 => 'diecisiete', 18 => 'dieciocho', 19 => 'diecinueve',
        20 => 'veinte', 21 => 'veintiuno', 22 => 'veintidós', 23 => 'veintitrés', 24 => 'veinticuatro',
        25 => 'veinticinco', 26 => 'veintiséis', 27 => 'veintisiete', 28 => 'veintiocho', 29 => 'veintinueve',
    ];

    private const DECENAS = [
        3 => 'treinta', 4 => 'cuarenta', 5 => 'cincuenta',
        6 => 'sesenta', 7 => 'setenta', 8 => 'ochenta', 9 => 'noventa',
    ];

    private const CENTENAS = [
        1 => 'ciento', 2 => 'doscientos', 3 => 'trescientos', 4 => 'cuatrocientos',
        5 => 'quinientos', 6 => 'seiscientos', 7 => 'setecientos', 8 => 'ochocientos', 9 => 'novecientos',
    ];

    public static function convert(float $monto, string $moneda = 'pesos', string $centavoMoneda = 'centavos'): string
    {
        $entero = (int) floor($monto);
        $centavos = (int) round(($monto - $entero) * 100);

        $partes = [];

        if ($entero === 0) {
            $partes[] = 'cero';
        } elseif ($entero === 100) {
            $partes[] = 'cien';
        } else {
            $partes[] = self::bloque($entero);
        }

        $partes[] = $moneda;

        if ($centavos > 0) {
            $partes[] = self::bloque($centavos);
            $partes[] = $centavoMoneda;
        }

        return trim(implode(' ', $partes));
    }

    /**
     * Convierte un número entero no-negativo a letras, manejando
     * millones / miles / centenas / decenas / unidades. Solo se
     * llama desde convert() y se asegura de que el input sea <= 999_999.
     */
    private static function bloque(int $n): string
    {
        if ($n === 0) {
            return 'cero';
        }

        $texto = '';

        // Millones
        if ($n >= 1_000_000) {
            $millones = (int) floor($n / 1_000_000);
            $texto .= self::tresDigitos($millones);
            $texto .= $millones === 1 ? ' millón' : ' millones';
            $n = $n % 1_000_000;
            if ($n > 0) {
                $texto .= ' ';
            }
        }

        // Miles
        if ($n >= 1000) {
            $miles = (int) floor($n / 1000);
            if ($miles === 1) {
                $texto .= 'mil';
            } else {
                $texto .= self::tresDigitos($miles) . ' mil';
            }
            $n = $n % 1000;
            if ($n > 0) {
                $texto .= ' ';
            }
        }

        // Centenas, decenas y unidades
        if ($n > 0) {
            $texto .= self::tresDigitos($n);
        }

        return $texto;
    }

    private static function tresDigitos(int $n): string
    {
        if ($n === 0) {
            return '';
        }

        if ($n === 100) {
            return 'cien';
        }

        $texto = '';

        // Centenas
        $centena = (int) floor($n / 100);
        $resto = $n % 100;

        if ($centena > 0) {
            $texto .= self::CENTENAS[$centena];
        }

        // Decenas y unidades
        if ($resto > 0) {
            if ($texto !== '') {
                $texto .= ' ';
            }
            if ($resto < 30) {
                $texto .= self::UNIDADES[$resto];
            } else {
                $decena = (int) floor($resto / 10);
                $unidad = $resto % 10;
                $texto .= self::DECENAS[$decena];
                if ($unidad > 0) {
                    $texto .= ' y ' . self::UNIDADES[$unidad];
                }
            }
        }

        return $texto;
    }
}
