<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class SubmitValue extends Model
{
    protected $table = 'submit_values';
    protected $guarded = ['id', 'created_at', 'updated_at'];
}
