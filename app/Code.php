<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class code extends Model
{
    protected $table = 'codes';
    protected $fillable = ['user_id', 'code'];
}
