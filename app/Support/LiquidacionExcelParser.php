<?php

namespace App\Support;

use PhpOffice\PhpSpreadsheet\IOFactory;

/**
 * Lee la hoja de liquidación en Excel que los operadores ya usaban antes de
 * que existiera la captura en el sistema (ver liquidacion.xlsx en la raíz
 * del proyecto, que es la plantilla original). En vez de leer por celda fija
 * (las copias reales varían un poco de fila), busca cada etiqueta conocida
 * y toma el primer valor no vacío a su derecha, deteniéndose si encuentra
 * otra etiqueta conocida antes de un valor (campo no llenado).
 */
class LiquidacionExcelParser
{
    /**
     * Todas las etiquetas de la plantilla, normalizadas. Sirve tanto para
     * ubicar el campo que buscamos como para saber cuándo detenernos al
     * barrer una fila (si lo siguiente es otra etiqueta, no es un valor).
     */
    private const ETIQUETAS = [
        'LIQUIDACION', 'NO. CONTRATO', 'NO. UNIDAD', 'ORIGEN', 'DESTINO', 'RECORRIDOS',
        'KM INICIAL', 'KM FINAL', 'TOTAL KM', 'FECHA SALIDA', 'FECHA REGRESO', 'OPERDOR', 'OPERADOR',
        'COSTO VIAJE', 'COMISION OPERADOR', 'GASTOS ENTREGADOS',
        'DIESEL VIAJE', 'LITROS VIAJE', 'DIESEL LLEGADA', 'LITROS LLEGADA',
        'TOTAL DIESEL', 'TOTAL LITROS', 'COSTO POR LITRO', 'RENDIMIENTO',
        'CASETAS', 'HOSPEDAJE', 'COMIDA', 'ESTACIONAMIENTO', 'LAVADA', 'OTROS GASTOS',
        'LIQUIDA O DEBEMOS', 'GASTOS TOTALES', 'TOTAL GASTOS', 'UTILIDAD TOTAL',
    ];

    /** Campos de texto (no se convierten a número). */
    private const CAMPOS_TEXTO = [
        'no_contrato' => 'NO. CONTRATO',
    ];

    private const CAMPOS = [
        'km_inicial'          => 'KM INICIAL',
        'km_final'             => 'KM FINAL',
        'costo_viaje'          => 'COSTO VIAJE',
        'gastos_entregados'    => 'GASTOS ENTREGADOS',
        'diesel_viaje_monto'   => 'DIESEL VIAJE',
        'diesel_viaje_litros'  => 'LITROS VIAJE',
        'diesel_llegada_monto' => 'DIESEL LLEGADA',
        'diesel_llegada_litros'=> 'LITROS LLEGADA',
        'casetas'              => 'CASETAS',
        'hospedaje'            => 'HOSPEDAJE',
        'comida'               => 'COMIDA',
        'estacionamiento'      => 'ESTACIONAMIENTO',
        'lavada'               => 'LAVADA',
        'otros_gastos'         => 'OTROS GASTOS',
    ];

    /**
     * @return array<string, float|null> una clave por cada campo en CAMPOS
     */
    public static function leer(string $rutaArchivo): array
    {
        $spreadsheet = IOFactory::load($rutaArchivo);
        $grid = $spreadsheet->getActiveSheet()->toArray(null, true, true, true);

        // Índice: etiqueta normalizada => ['fila' => int, 'col' => string]
        $posiciones = [];
        foreach ($grid as $fila => $columnas) {
            foreach ($columnas as $col => $valor) {
                $norm = self::normalizar($valor);
                if ($norm !== '' && in_array($norm, self::ETIQUETAS, true) && ! isset($posiciones[$norm])) {
                    $posiciones[$norm] = ['fila' => $fila, 'col' => $col];
                }
            }
        }

        $resultado = [];
        foreach (self::CAMPOS as $campo => $etiqueta) {
            $resultado[$campo] = self::valorDeEtiqueta($grid, $posiciones, $etiqueta, fn ($v) => self::aNumero($v));
        }
        foreach (self::CAMPOS_TEXTO as $campo => $etiqueta) {
            $resultado[$campo] = self::valorDeEtiqueta($grid, $posiciones, $etiqueta, fn ($v) => trim((string) $v));
        }

        return $resultado;
    }

    private static function valorDeEtiqueta(array $grid, array $posiciones, string $etiqueta, callable $convertir): mixed
    {
        if (! isset($posiciones[$etiqueta])) {
            return null;
        }

        $fila = $posiciones[$etiqueta]['fila'];
        $colEtiqueta = $posiciones[$etiqueta]['col'];
        $columnas = array_keys($grid[$fila] ?? []);
        $i = array_search($colEtiqueta, $columnas, true);

        if ($i === false) {
            return null;
        }

        for ($j = $i + 1; $j < count($columnas); $j++) {
            $valor = $grid[$fila][$columnas[$j]] ?? null;
            $norm = self::normalizar($valor);

            if ($norm === '' || $norm === '$') {
                continue;
            }

            if (in_array($norm, self::ETIQUETAS, true)) {
                return null; // llegamos a la siguiente etiqueta sin encontrar valor
            }

            return $convertir($valor);
        }

        return null;
    }

    private static function normalizar(mixed $valor): string
    {
        if (! is_string($valor) && ! is_numeric($valor)) {
            return '';
        }

        $texto = trim((string) $valor);
        $texto = preg_replace('/\s+/', ' ', $texto);
        $texto = strtoupper($texto);
        $texto = strtr($texto, ['Á' => 'A', 'É' => 'E', 'Í' => 'I', 'Ó' => 'O', 'Ú' => 'U', 'Ñ' => 'N']);

        return $texto;
    }

    private static function aNumero(mixed $valor): ?float
    {
        if (is_numeric($valor)) {
            return (float) $valor;
        }

        if (is_string($valor)) {
            $limpio = preg_replace('/[^0-9.\-]/', '', $valor);

            return $limpio === '' ? null : (float) $limpio;
        }

        return null;
    }
}
