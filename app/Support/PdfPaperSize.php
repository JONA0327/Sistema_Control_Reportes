<?php

namespace App\Support;

class PdfPaperSize
{
    /**
     * Tamaño de papel para Dompdf ->setPaper(...).
     *
     * @return array{0: string|array<int, int>, 1: string}
     */
    public static function forDompdf(string $tamano): array
    {
        return match ($tamano) {
            'oficio'      => [[0, 0, 612, 936], 'portrait'], // 8.5" x 13"
            'media_carta' => [[0, 0, 396, 612], 'portrait'], // Media carta (estilo pagaré), 5.5" x 8.5"
            default       => ['letter', 'portrait'],          // Carta, 8.5" x 11"
        };
    }
}
