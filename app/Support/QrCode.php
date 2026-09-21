<?php

namespace App\Support;

use SimpleSoftwareIO\QrCode\Generator;

class QrCode
{
    public static function svg(string $data, int $size = 180): string
    {
        return (string) app(Generator::class)
            ->format('svg')
            ->size($size)
            ->margin(2)
            ->errorCorrection('H')
            ->generate($data);
    }
}
