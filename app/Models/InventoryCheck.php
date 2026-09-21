<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class InventoryCheck extends Model
{
    use HasFactory;

    public $timestamps = false;

    public const LAB_UNIT_NAMES = [
        'Laboratorium Farmasi',
        'Laboratorium Kebidanan',
        'Laboratorium Gizi',
    ];

    protected $fillable = [
        'unit_id',
        'created_by',
        'check_code',
        'periode',
        'semester',
        'tahun_akademik',
        'tanggal_pemeriksaan',
        'status',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'unit_id' => 'integer',
            'created_by' => 'integer',
            'tanggal_pemeriksaan' => 'date',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (InventoryCheck $check): void {
            $check->periode = $check->tahun_akademik;
        });
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function checker(): BelongsTo
    {
        return $this->creator();
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function items(): HasMany
    {
        return $this->hasMany(InventoryCheckItem::class);
    }

    public function scopeVisibleFor(Builder $query, User $user): Builder
    {
        return $user->isUnitScoped()
            ? $query->where('unit_id', $user->unit_id)
            : $query;
    }
}
