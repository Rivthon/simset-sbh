<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryCheckItem extends Model
{
    use HasFactory;

    public $timestamps = false;

    protected $fillable = [
        'inventory_check_id',
        'asset_id',
        'jumlah_sistem',
        'jumlah_aktual',
        'jumlah_baik',
        'jumlah_sedang',
        'jumlah_rusak',
        'jumlah_hilang',
        'hasil_pemeriksaan',
    ];

    protected function casts(): array
    {
        return [
            'jumlah_sistem' => 'integer',
            'jumlah_aktual' => 'integer',
            'jumlah_baik' => 'integer',
            'jumlah_sedang' => 'integer',
            'jumlah_rusak' => 'integer',
            'jumlah_hilang' => 'integer',
        ];
    }

    public function inventoryCheck(): BelongsTo
    {
        return $this->belongsTo(InventoryCheck::class);
    }

    public function check(): BelongsTo
    {
        return $this->belongsTo(InventoryCheck::class, 'inventory_check_id');
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }
}
