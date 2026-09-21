<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class Asset extends Model
{
    use HasFactory;

    public $timestamps = false;

    public const KONDISI_ASET_LABELS = [
        'baik' => 'Baik',
        'sedang' => 'Sedang',
        'rusak' => 'Rusak',
        'hilang' => 'Hilang',
    ];

    public const SATUAN_OPTIONS = [
        'unit' => 'unit',
        'pcs' => 'pcs',
        'set' => 'set',
        'buah' => 'buah',
        'lembar' => 'lembar',
        'pasang' => 'pasang',
        'box' => 'box',
        'lainnya' => 'lainnya',
    ];

    protected $fillable = [
        'unit_id',
        'category_id',
        'location_id',
        'container_id',
        'identification_type',
        'quantity',
        'satuan',
        'created_by',
        'asset_code',
        'legacy_inventory_code',
        'qr_code',
        'name',
        'kondisi_aset',
        'jumlah_baik',
        'jumlah_sedang',
        'jumlah_rusak',
        'jumlah_hilang',
        'description',
    ];

    protected function casts(): array
    {
        return [
            'quantity' => 'integer',
            'jumlah_baik' => 'integer',
            'jumlah_sedang' => 'integer',
            'jumlah_rusak' => 'integer',
            'jumlah_hilang' => 'integer',
        ];
    }

    protected static function booted(): void
    {
        static::saving(function (Asset $asset): void {
            $asset->identification_type ??= 'individual';
            if (! in_array($asset->identification_type, ['individual', 'group'], true)) {
                $asset->identification_type = 'group';
            }

            $asset->quantity = max(1, (int) ($asset->quantity ?: 1));

            if (in_array($asset->identification_type, ['individual', 'group'], true) && blank($asset->qr_code)) {
                $asset->qr_code = self::generateQrCode();
            }

            $asset->satuan ??= 'unit';
            $counts = [
                'baik' => (int) $asset->jumlah_baik,
                'sedang' => (int) $asset->jumlah_sedang,
                'rusak' => (int) $asset->jumlah_rusak,
                'hilang' => (int) $asset->jumlah_hilang,
            ];

            if (array_sum($counts) === 0) {
                $condition = self::normalizeKondisiAset($asset->kondisi_aset) ?? 'baik';
                $asset->{'jumlah_'.$condition} = $asset->quantity;
                $counts[$condition] = $asset->quantity;
            }

            $asset->kondisi_aset = self::worstCondition($counts);
        });
    }

    public function getKondisiAsetLabelAttribute(): string
    {
        return $this->kondisiAsetLabel();
    }

    public function kondisiAsetLabel(): string
    {
        return $this->conditionSummaryLabel();
    }

    public function conditionSummaryLabel(): string
    {
        $counts = $this->conditionCounts();
        $parts = [];

        foreach (self::KONDISI_ASET_LABELS as $condition => $label) {
            $total = (int) ($counts[$condition] ?? 0);

            if ($total <= 0) {
                continue;
            }

            $parts[] = $this->quantity > 1 ? "{$total} {$label}" : $label;
        }

        return $parts !== [] ? implode(', ', $parts) : self::KONDISI_ASET_LABELS['baik'];
    }

    public function conditionCounts(): array
    {
        return [
            'baik' => (int) $this->jumlah_baik,
            'sedang' => (int) $this->jumlah_sedang,
            'rusak' => (int) $this->jumlah_rusak,
            'hilang' => (int) $this->jumlah_hilang,
        ];
    }

    public static function worstCondition(array $counts): string
    {
        return match (true) {
            (int) ($counts['hilang'] ?? 0) > 0 => 'hilang',
            (int) ($counts['rusak'] ?? 0) > 0 => 'rusak',
            (int) ($counts['sedang'] ?? 0) > 0 => 'sedang',
            default => 'baik',
        };
    }

    public function conditionHas(string $condition): bool
    {
        $condition = self::normalizeKondisiAset($condition);

        if ($condition === null) {
            return false;
        }

        return (int) ($this->conditionCounts()[$condition] ?? 0) > 0;
    }

    public function worstConditionFromCounts(): string
    {
        $counts = $this->conditionCounts();

        return match (true) {
            (int) ($counts['hilang'] ?? 0) > 0 => 'hilang',
            (int) ($counts['rusak'] ?? 0) > 0 => 'rusak',
            (int) ($counts['sedang'] ?? 0) > 0 => 'sedang',
            default => 'baik',
        };
    }

    public function scopeWhereConditionHas(Builder $query, string $condition): Builder
    {
        $kondisi = self::normalizeKondisiAset($condition);

        if ($kondisi === null) {
            return $query;
        }

        $column = match ($kondisi) {
            'baik' => 'jumlah_baik',
            'sedang' => 'jumlah_sedang',
            'rusak' => 'jumlah_rusak',
            'hilang' => 'jumlah_hilang',
        };

        return $query->where($column, '>', 0);
    }

    public function scopeWhereAnyConditionHas(Builder $query, array $conditions): Builder
    {
        $conditions = collect($conditions)
            ->map(fn ($condition) => self::normalizeKondisiAset((string) $condition))
            ->filter()
            ->unique()
            ->values();

        if ($conditions->isEmpty()) {
            return $query;
        }

        $columns = [
            'baik' => 'jumlah_baik',
            'sedang' => 'jumlah_sedang',
            'rusak' => 'jumlah_rusak',
            'hilang' => 'jumlah_hilang',
        ];

        return $query->where(function (Builder $query) use ($conditions, $columns): void {
            foreach ($conditions as $condition) {
                $query->orWhere($columns[$condition], '>', 0);
            }
        });
    }

    public static function normalizeKondisiAset(?string $value): ?string
    {
        $normalized = self::normalizeRawKondisiValue($value);

        if (array_key_exists((string) $normalized, self::KONDISI_ASET_LABELS)) {
            return $normalized;
        }

        $mapped = match ($normalized) {
            'good' => 'baik',
            'minor_damage' => 'sedang',
            'major_damage', 'damaged', 'damage' => 'rusak',
            'lost', 'missing' => 'hilang',
            'inactive', 'disabled' => 'rusak',
            default => null,
        };

        if ($mapped !== null) {
            return $mapped;
        }

        foreach (array_keys(self::KONDISI_ASET_LABELS) as $kondisi) {
            if (str_contains($normalized, $kondisi)) {
                return $kondisi;
            }
        }

        return null;
    }

    private static function normalizeRawKondisiValue(?string $value): ?string
    {
        if ($value === null) {
            return null;
        }

        $normalized = strtolower(trim($value));

        return $normalized === '' ? null : $normalized;
    }

    public function getKodeAsetSistemAttribute(): ?string
    {
        return $this->asset_code;
    }

    public function getKodeAsetLamaAttribute(): ?string
    {
        return $this->legacy_inventory_code;
    }

    public function getNamaAsetAttribute(): ?string
    {
        return $this->name;
    }

    public function getJumlahAttribute(): int
    {
        return (int) $this->quantity;
    }

    public function getMetodeIdentifikasiAttribute(): ?string
    {
        return $this->identification_type;
    }

    public function identificationLabel(): string
    {
        return match ($this->identification_type) {
            'group' => 'QR Kelompok',
            default => 'QR Individual',
        };
    }

    public function identificationDescription(): string
    {
        return match ($this->identification_type) {
            'group' => 'Satu QR untuk beberapa aset sejenis dalam satu kelompok',
            default => 'Satu QR ditempel langsung untuk satu aset',
        };
    }

    public static function generateQrCode(): string
    {
        do {
            $code = 'SIMASET-AST-'.Str::upper(Str::random(10));
        } while (
            self::where('qr_code', $code)->exists()
            || Container::where('qr_code', $code)->exists()
        );

        return $code;
    }

    public static function generateAssetCode(Unit $unit, Category|string $category): string
    {
        $typeCode = $category instanceof Category
            ? self::categoryCode($category)
            : self::categoryCodeFromName($category);
        $unitCode = preg_replace('/[^A-Z0-9]/', '', strtoupper($unit->code ?: 'U'.$unit->id));
        $prefix = 'AST-'.$unitCode.'-'.$typeCode.'-';

        $lastNumber = self::where('asset_code', 'like', $prefix.'%')
            ->selectRaw('MAX(CAST(SUBSTRING(asset_code, ?) AS UNSIGNED)) as number', [strlen($prefix) + 1])
            ->value('number');

        return $prefix.str_pad(((int) $lastNumber) + 1, 4, '0', STR_PAD_LEFT);
    }

    public static function categoryCode(Category $category): string
    {
        return self::categoryCodeFromName((string) $category->name);
    }

    public static function categoryCodeFromName(string $name): string
    {
        $normalized = Str::of($name)
            ->lower()
            ->replaceMatches('/[^a-z0-9\s]+/', ' ')
            ->squish()
            ->toString();

        $overrides = [
            'peralatan' => 'PRT',
            'perlengkapan' => 'PLK',
            'alat ukur' => 'AUK',
            'alat praktik' => 'APR',
            'alat praktikum' => 'APR',
            'alat laboratorium' => 'ALB',
            'instrumen' => 'INS',
            'media praktikum' => 'MPR',
            'perabot' => 'PRB',
            'lain lain' => 'LLN',
        ];

        if (isset($overrides[$normalized])) {
            return $overrides[$normalized];
        }

        $words = array_values(array_filter(explode(' ', $normalized)));

        if ($words === []) {
            return 'KTG';
        }

        $code = count($words) === 1
            ? preg_replace('/[^bcdfghjklmnpqrstvwxyz0-9]/i', '', $words[0])
            : implode('', array_map(fn (string $word): string => $word[0], $words));

        foreach (array_reverse($words) as $word) {
            if (strlen($code) >= 3) {
                break;
            }

            $code .= preg_replace('/[^bcdfghjklmnpqrstvwxyz0-9]/i', '', substr($word, 1));
        }

        $code = preg_replace('/[^A-Z0-9]/', '', strtoupper($code));

        return substr(str_pad($code ?: 'KTG', 3, 'X'), 0, 3);
    }

    public function hasTransactionHistory(): bool
    {
        return $this->inventoryCheckItems()->exists();
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class);
    }

    public function container(): BelongsTo
    {
        return $this->belongsTo(Container::class);
    }

    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    public function inventoryCheckItems(): HasMany
    {
        return $this->hasMany(InventoryCheckItem::class);
    }

    public function completedInventoryCheckItems(): HasMany
    {
        return $this->hasMany(InventoryCheckItem::class)
            ->whereNotNull('jumlah_aktual');
    }

    public function latestCompletedInventoryCheckItem(): HasOne
    {
        return $this->hasOne(InventoryCheckItem::class)
            ->whereNotNull('jumlah_aktual')
            ->latestOfMany();
    }

    public function toolReplacementRequests(): HasMany
    {
        return $this->hasMany(ToolReplacementRequest::class);
    }

    public function quantityAdditions(): HasMany
    {
        return $this->hasMany(AssetQuantityAddition::class);
    }

    public function scopeVisibleFor(Builder $query, User $user): Builder
    {
        return $user->isUnitScoped()
            ? $query->where('unit_id', $user->unit_id)
            : $query;
    }

}
