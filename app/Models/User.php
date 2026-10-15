<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Attributes\Fillable;
use Illuminate\Database\Eloquent\Attributes\Hidden;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Validation\ValidationException;

#[Fillable(['name', 'email', 'password', 'role', 'is_active'])]
#[Hidden(['password', 'remember_token'])]
class User extends Authenticatable implements FilamentUser
{
    use Notifiable;

    public function canAccessCms(): bool
    {
        return $this->is_active && in_array($this->role, ['admin', 'editor'], true);
    }

    public function canAccessPanel(Panel $panel): bool
    {
        return $panel->getId() === 'admin' && $this->canAccessCms();
    }

    protected static function booted(): void
    {
        $protect = function (self $user): void {
            if ($user->getOriginal('role') === 'admin' && $user->getOriginal('is_active') && self::where('role', 'admin')->where('is_active', true)->whereKeyNot($user->id)->lockForUpdate()->get()->isEmpty()) {
                throw ValidationException::withMessages(['role' => 'Keep at least one active administrator.']);
            }
        };
        static::deleting($protect);
        static::updating(function (self $user) use ($protect): void {
            if ($user->isDirty(['role', 'is_active']) && ($user->role !== 'admin' || ! $user->is_active)) {
                $protect($user);
            }
        });
    }

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_active' => 'boolean',
        ];
    }
}
