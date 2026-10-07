<?php

namespace App\Support;

class MoneyNormalizer
{
    // ponytail: assumes amounts are meant to be round hundreds (this business
    // never charges partial pesos); snaps a 1-3 peso typo back to the nearest
    // hundred. If fractional pricing is ever introduced, drop this.
    public static function snapToHundred(int $value): int
    {
        $nearest = (int) round($value / 100) * 100;
        $diff = abs($value - $nearest);

        return ($diff >= 1 && $diff <= 3) ? $nearest : $value;
    }
}
