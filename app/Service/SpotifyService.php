<?php
namespace App\Services;

use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;

class SpotifyService {
    public static function getPlaylist() {
        /** @var \App\User $user */
        $user = Auth::user();
        $dsa = $user->spotifyDsa();

        return Http::get('https://api.spotify.com/v1/playlists/'.$dsa->provider_id.'');

    }
}

