<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class User extends Model
{
    protected $table = 'users';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public const ROLES = ['システム管理者', '一般', '閲覧'];
}
