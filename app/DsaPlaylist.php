<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DsaPlaylist extends Model
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'user_id', 'dsa_playlist_id', 'description', 'name', 'image_url', 'provider'
    ];

    protected $casts = [
        'image_url' => 'array'
    ];
    public function user()
    {
        $this->belongsTo(User::class);
    }
    public function track()
    {
        return $this->hasMany(Track::class);
    }
}
