<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

#[Hidden(['password'])]
class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    public $timestamps = false;

    protected $fillable = [
        'unit_id',
        'name',
        'username',
        'email',
        'password',
        'role',
        'status',
    ];

    public function unit(): BelongsTo
    {
        return $this->belongsTo(Unit::class);
    }

    public function managedAssets(): HasMany
    {
        return $this->hasMany(Asset::class, 'created_by');
    }

    public function verifiedToolReplacementRequests(): HasMany
    {
        return $this->hasMany(ToolReplacementRequest::class, 'verified_by');
    }

    public function isActive(): bool
    {
        return $this->status === 'active';
    }

    public function hasRole(string|array $roles): bool
    {
        $currentRole = $this->role === 'laboran' ? 'pengelola' : $this->role;
        $roles = array_map(
            fn (string $role): string => $role === 'laboran' ? 'pengelola' : $role,
            (array) $roles,
        );

        return in_array($currentRole, $roles, true);
    }

    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    public function isPengelola(): bool
    {
        return in_array($this->role, ['pengelola', 'laboran'], true);
    }

    public function isLaboran(): bool
    {
        return $this->isPengelola();
    }

    public function isPimpinan(): bool
    {
        return $this->role === 'pimpinan';
    }

    public function hasAllAccess(): bool
    {
        return $this->isAdmin() || $this->isPimpinan() || $this->getAttribute('access_scope') === 'all';
    }

    public function isUnitScoped(): bool
    {
        return $this->isPengelola() && ! $this->hasAllAccess();
    }

    public function isUnitScopedManager(): bool
    {
        return $this->isPengelola() && ! $this->hasAllAccess();
    }

    public function canFilterUnits(): bool
    {
        return $this->isAdmin() || $this->hasAllAccess();
    }

    public function canManageData(): bool
    {
        return $this->hasRole(['admin', 'pengelola']);
    }

    public function isReadOnly(): bool
    {
        return $this->isPimpinan();
    }

    public function canAccessUnit(?int $unitId): bool
    {
        return $this->isAdmin()
            || $this->hasAllAccess()
            || ($this->unit_id !== null && $this->unit_id === $unitId);
    }

    public function canDeleteAssetInUnit(?int $unitId): bool
    {
        return $this->isAdmin()
            || ($this->isPengelola() && $this->unit_id !== null && $this->unit_id === $unitId);
    }

    public function canReportAssetInUnit(?int $unitId): bool
    {
        return ! $this->isPimpinan()
            && $this->hasRole(['admin', 'pengelola'])
            && $this->canAccessUnit($unitId);
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'password' => 'hashed',
        ];
    }
}
