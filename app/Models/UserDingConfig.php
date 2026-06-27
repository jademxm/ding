<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserDingConfig extends Model
{
    //
    const STATUS_ENABLE = 1; // 启用
    const STATUS_DISABLE = 2;// 禁用

    protected $fillable = [
        'user_id',
        'name',
        'webhook',
        'secret',
        'status',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
