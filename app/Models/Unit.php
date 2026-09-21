<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Unit extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'name',
        'code',
        'status',
    ];

    public static function generateCode(string $name): string
    {
        $words = preg_split('/\s+/', strtoupper(trim($name))) ?: [];
        $base = collect($words)
            ->filter()
            ->map(fn (string $word) => preg_replace('/[^A-Z0-9]/', '', substr($word, 0, 3)))
            ->implode('');

        $base = substr($base ?: 'UNIT', 0, 12);
        $code = $base;
        $counter = 2;

        while (self::where('code', $code)->exists()) {
            $code = $base.'-'.str_pad((string) $counter, 2, '0', STR_PAD_LEFT);
            $counter++;
        }

        return $code;
    }

    public function users(): HasMany
    {
        return $this->hasMany(User::class);
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function inventoryChecks(): HasMany
    {
        return $this->hasMany(InventoryCheck::class);
    }

    public function toolReplacementRequests(): HasMany
    {
        return $this->hasMany(ToolReplacementRequest::class);
    }
}
