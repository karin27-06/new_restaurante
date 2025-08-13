<?php

namespace App\Helpers;

class NumeroALetras
{
    public static function convertir($numero)
    {
        // Si el número es mayor que 0
        $formatter = new \NumberFormatter('es_PE', \NumberFormatter::SPELLOUT);
        return ucfirst($formatter->format($numero));
    }
}
