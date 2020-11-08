<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class FormGroup extends Model
{
    protected $table = 'form_groups';

    public function items()
    {
        return $this->hasMany('App\Models\FormItem', 'form_group_id');
    }
}
