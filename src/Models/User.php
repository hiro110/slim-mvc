<?php

namespace App\Models;

use Psr\Container\ContainerInterface;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Capsule\Manager;

class User extends Model
{
    protected $table = 'users';
}
