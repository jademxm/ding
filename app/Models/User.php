<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    //
    const STATUS_ENABLE = 1; // 启用
    const STATUS_DISABLE = 2;// 禁用

    protected $fillable = ['name', 'phone', 'status', 'balance'];
}
