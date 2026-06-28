<?php

namespace App\Models;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;

class User extends Authenticatable implements FilamentUser
{
    //
    const STATUS_ENABLE = 1; // 启用
    const STATUS_DISABLE = 2;// 禁用

    protected $fillable = ['name', 'phone', 'status', 'balance', 'password'];

    protected $hidden   = ['password'];

    public function canAccessPanel(Panel $panel): bool
    {
        if ($panel->getId() === 'admin') return false; // users 不能进后台
        return $this->status == 1;                      // 启用可进 /app
    }
}
