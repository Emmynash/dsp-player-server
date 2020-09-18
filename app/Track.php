<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class Track extends Model
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'playlist_id', 'snapshot_id',
    ];


    public function dsaPlaylist()
    {
        $this->belongsTo(DsaPlaylist::class);
    }
}
