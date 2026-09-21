<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class AssetQuantityAddition extends Model
{
    protected $fillable = [
        'asset_id',
        'created_by',
        'addition_date',
        'quantity',
        'jumlah_baik',
        'jumlah_sedang',
        'jumlah_rusak',
        'jumlah_hilang',
        'notes',
    ];

    protected function casts(): array
    {
        return [
            'addition_date' => 'date',
            'quantity' => 'integer',
            'jumlah_baik' => 'integer',
            'jumlah_sedang' => 'integer',
            'jumlah_rusak' => 'integer',
            'jumlah_hilang' => 'integer',
        ];
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }
}
