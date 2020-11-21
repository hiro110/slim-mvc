<?php

namespace App\Models;

// use Psr\Container\ContainerInterface;
use Illuminate\Database\Eloquent\Model;
// use Illuminate\Database\Capsule\Manager;

class User extends Model
{
    protected $table = 'users';
    protected $guarded = ['id', 'created_at', 'updated_at'];

    public const ROLES = ['システム管理者', '一般', '閲覧'];
}
