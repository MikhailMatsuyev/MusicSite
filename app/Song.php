<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Song extends Model
{
    protected $fillable = [
        'name', 'album_id', 'artist_id'
    ];
    
    public function album()
    {
        return $this->belongsTo('App\Album');
    }
}
