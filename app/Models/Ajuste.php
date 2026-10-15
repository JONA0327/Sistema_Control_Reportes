<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Carbon;

/** Ajustes globales clave/valor. */
class Ajuste extends Model
{
    public const CORTE_HISTORICO = 'corte_historico_hasta';

    protected $primaryKey = 'clave';

    public $incrementing = false;

    protected $keyType = 'string';

    protected $fillable = ['clave', 'valor'];

    private static array $cache = [];

    public static function valor(string $clave): ?string
    {
        return static::$cache[$clave] ??= static::find($clave)?->valor;
    }

    public static function guardar(string $clave, ?string $valor): void
    {
        static::updateOrCreate(['clave' => $clave], ['valor' => $valor]);
        static::$cache[$clave] = $valor;
    }

    /** Fecha hasta la cual (inclusive) los contratos son históricos; null = corte apagado. */
    public static function corteHistorico(): ?Carbon
    {
        $v = static::valor(self::CORTE_HISTORICO);

        return $v ? Carbon::parse($v)->startOfDay() : null;
    }
}
