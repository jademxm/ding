<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserStock extends Model
{
    //
    const STATUS_ENABLE = 1; // 启用
    const STATUS_DISABLE = 2;// 禁用
}
