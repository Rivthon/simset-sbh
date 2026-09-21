<?php

namespace App\Support;

use Illuminate\Support\Str;

class AssetClassifier
{
    public static function isConsumable(array|string $value): bool
    {
        $text = is_array($value) ? implode(' ', $value) : $value;
        $text = Str::of($text)->lower()->toString();

        foreach (self::consumableKeywords() as $keyword) {
            if (str_contains($text, $keyword)) {
                return true;
            }
        }

        return false;
    }

    public static function consumableKeywords(): array
    {
        return [
            'bhp',
            'bahan habis pakai',
            'kapas',
            'alkohol',
            'masker',
            'handscoon',
            'sarung tangan',
            'spuit',
            'disposable',
            'reagen',
            'bahan kimia',
            'tisu',
            'kasa',
            'sekali pakai',
        ];
    }
}
