<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ToolReplacementRequest extends Model
{
    use HasFactory;

    public const STATUS_OPTIONS = [
        'menunggu_verifikasi' => 'Menunggu Verifikasi',
        'menunggu_penggantian' => 'Menunggu Penggantian',
        'sudah_diganti' => 'Sudah Diganti',
        'ditolak' => 'Ditolak',
    ];

    public const STATUS_LABELS = [
        ...self::STATUS_OPTIONS,
        'pending' => 'Menunggu Verifikasi',
        'waiting_replacement' => 'Menunggu Penggantian',
        'replaced' => 'Sudah Diganti',
        'rejected' => 'Ditolak',
    ];

    public const STATUS_MESSAGES = [
        'menunggu_verifikasi' => 'Data surat penggantian alat sudah diterima dan sedang menunggu verifikasi laboran.',
        'menunggu_penggantian' => 'Data sudah diverifikasi. Silakan melakukan penggantian alat sesuai arahan laboran.',
        'sudah_diganti' => 'Penggantian alat telah diterima dan dinyatakan selesai oleh laboran.',
        'ditolak' => 'Pengajuan tidak dapat diproses. Silakan hubungi laboran terkait untuk informasi lebih lanjut.',
    ];

    protected $fillable = [
        'replacement_code',
        'asset_id',
        'unit_id',
        'student_name',
        'student_nim',
        'student_semester',
        'prodi_kelas',
        'practicum_name',
        'incident_date',
        'replacement_quantity',
        'damage_description',
        'whatsapp_number',
        'damage_photo',
        'status',
        'verified_by',
        'verified_at',
        'rejection_reason',
        'received_at',
        'laboran_note',
    ];

    protected function casts(): array
    {
        return [
            'incident_date' => 'date',
            'replacement_quantity' => 'integer',
            'verified_at' => 'datetime',
            'received_at' => 'datetime',
        ];
    }

    protected static function booted(): void
    {
        static::creating(function (ToolReplacementRequest $request): void {
            if (blank($request->replacement_code) && $request->unit_id && $request->incident_date) {
                $request->replacement_code = self::generateReplacementCode(
                    Unit::find($request->unit_id),
                    $request->incident_date,
                );
            }

        });
    }

    public function getStatusLabelAttribute(): string
    {
        return self::STATUS_LABELS[$this->status] ?? $this->status;
    }

    public function getStatusMessageAttribute(): string
    {
        return self::STATUS_MESSAGES[$this->status] ?? 'Status penggantian alat sedang diproses.';
    }

    public function getDisplayNimAttribute(): string
    {
        return filled($this->student_nim) ? (string) $this->student_nim : '-';
    }

    public function getDisplaySemesterAttribute(): string
    {
        return filled($this->student_semester) ? (string) $this->student_semester : '-';
    }

    public function getDisplayNimSemesterAttribute(): string
    {
        return $this->display_nim.'/'.$this->display_semester;
    }

    public static function generateReplacementCode(?Unit $unit, mixed $incidentDate): string
    {
        $date = $incidentDate instanceof \DateTimeInterface
            ? $incidentDate
            : \Illuminate\Support\Carbon::parse($incidentDate);
        $rawUnitCode = $unit?->code ?: ($unit?->id ? 'U'.$unit->id : 'UNIT');
        $unitCode = preg_replace('/[^A-Z0-9]/', '', strtoupper($rawUnitCode)) ?: 'UNIT';
        $prefix = 'PGA-'.$unitCode.'-'.$date->format('m').'-'.$date->format('Y').'-';
        $lastNumber = self::where('replacement_code', 'like', $prefix.'%')
            ->selectRaw('MAX(CAST(SUBSTRING(replacement_code, ?) AS UNSIGNED)) as number', [strlen($prefix) + 1])
            ->value('number');

        return $prefix.str_pad(((int) $lastNumber) + 1, 3, '0', STR_PAD_LEFT);
    }

    public function asset(): BelongsTo
    {
        return $this->belongsTo(Asset::class);
    }

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }

    public function scopeVisibleFor(Builder $query, User $user): Builder
    {
        return $user->isUnitScoped()
            ? $query->where('unit_id', $user->unit_id)
            : $query;
    }
}
