<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Submit extends Model
{
    protected $table = 'submits';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
