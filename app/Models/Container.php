<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOneThrough;
use Illuminate\Support\Str;

class Container extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'unit_id',
        'location_id',
        'name',
        'code',
        'qr_code',
        'status',
    ];

    protected static function booted(): void
    {
        static::creating(function (Container $container): void {
            if (blank($container->qr_code)) {
                $container->qr_code = self::generateQrCode();
            }
        });
    }

    public static function generateQrCode(): string
    {
        do {
            $code = 'SIMASET-CTR-'.Str::upper(Str::random(10));
        } while (
            self::where('qr_code', $code)->exists()
            || Asset::where('qr_code', $code)->exists()
        );

        return $code;
    }

    public static function generateCode(Location $location): string
    {
        $unitCode = preg_replace('/[^A-Z0-9]/', '', strtoupper($location->unit?->code ?: 'U'.$location->unit_id));
        $prefix = 'TPN-'.$unitCode.'-';
        $lastNumber = self::where('code', 'like', $prefix.'%')
            ->selectRaw('MAX(CAST(SUBSTRING(code, ?) AS UNSIGNED)) as number', [strlen($prefix) + 1])
            ->value('number');

        return $prefix.str_pad(((int) $lastNumber) + 1, 4, '0', STR_PAD_LEFT);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function locationUnit(): HasOneThrough
    {
        return $this->hasOneThrough(
            Unit::class,
            Location::class,
            'id',
            'id',
            'location_id',
            'unit_id',
        );
    }

    public function assets(): HasMany
    {
        return $this->hasMany(Asset::class);
    }

    public function scopeVisibleFor(Builder $query, User $user): Builder
    {
        return $user->isUnitScoped()
            ? $query->where('unit_id', $user->unit_id)
            : $query;
    }
}
