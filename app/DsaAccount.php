<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;

class DsaAccount extends Model
{
    use Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
         'user_id', 'provider_id', 'oauth_refresh_token', 'oauth_token', 'provider'
    ];

    public function user()
    {
        $this->belongsTo(User::class);
    }
}
