<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStock extends Model
{
    //
    const STATUS_ENABLE = 1; // 启用
    const STATUS_DISABLE = 2;// 禁用

    protected $fillable = [
        'user_id',
        'name',
        'code',
        'status',
        'order',
        'min',
        'max',
        'last_alert_at',
        'last_alert_price'
    ];

    /** 关联用户 */
    public function user()
    {
        return $this->belongsTo(User::class);
    }
}
