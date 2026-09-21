<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Location extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'unit_id',
        'name',
        'status',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function containers(): HasMany
    {
        return $this->hasMany(Container::class);
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
