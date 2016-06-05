<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Playlist extends Model
{
    protected $fillable = [
        'name', 'user_id', 'song_id'
    ];
    
    protected $hidden = ['user_id'];
}
