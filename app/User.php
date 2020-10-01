<?php

namespace App;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Passport\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'email', 'password', 'dsa_id', 'username'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
    ];


    public function dsaAccounts()
    {
        return $this->hasMany(DsaAccount::class);
    }

    public function spotifyDsa()
    {
        return $this->dsaAccounts()->where('provider', 'spotify')->first();
    }

    public function appleDsa()
    {
       return $this->dsaAccounts()->where('provider', 'apple')->first();
    }

    public function createDsaAccount(string $provider, string $dsaId, string $dsaOAuthToken, string $dsaOAuthRefreshToken)
    {
        return DsaAccount::create([
            'user_id' => $this->id,
            'provider_id' => $dsaId,
            'provider' => $provider,
            'oauth_token' => $dsaOAuthToken,
            'oauth_refresh_token' => $dsaOAuthRefreshToken,
        ]);
    }

    public function dsaPlaylist()
    {
        return $this->hasMany(DsaAccount::class);
    }

    public function createPlaylist($dsaId, $imageUrl, $name, $description, $provider)
    {

        return DsaPlaylist::create([
            'user_id' => $this->id,
            'name' => $name,
            'image_url' => $imageUrl,
            'description' => $description,
            'dsa_playlist_id' => $dsaId,
            'provider' => $provider
        ]);
    }

    public function addPlaylistTrack( $playlistId, $snapshotId)
    {
        return Track::create([
            'playlist_id' => $playlistId,
            'snapshot_id' => $snapshotId
        ]);
    }
}
