<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Album extends Model
{
    protected $fillable = ['name','artist_id','photo', 'year'];
    
    public function songs()
    {       
        return $this->hasMany('App\Song');
    }
}
