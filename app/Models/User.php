<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable
{
    use HasFactory, Notifiable, HasRoles; // Добавяме HasRoles за Spatie

    protected $fillable = [
        'name',
        'email',
        'password',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    // 🔹 Връзка с ключовете (Keys)
    public function keys(): HasMany
    {
        return $this->hasMany(Key::class);
    }

    // 🔹 Връзка с отборите (Teams)
    public function teams(): BelongsToMany
    {
        return $this->belongsToMany(Team::class, 'user_role_team');
    }

    // 🔹 Връзка с ролите (Roles) - използва Spatie
    public function roles(): BelongsToMany
    {
        return $this->belongsToMany(Role::class, 'user_role_team');
    }
}
