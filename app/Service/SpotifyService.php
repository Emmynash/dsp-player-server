<?php
namespace App\Services;

use App\DsaPlaylist;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use GuzzleHttp\Client;

class SpotifyService {
    public static function createPlaylist(Request $request)
    {
        /** @var \App\User $user */
        $user = Auth::user();
        $dsa = $user->spotifyDsa();

        $validatedData = $request->validate([
            'name' => 'required|string',
            'description' => 'string',
        ]);

        $data = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization'=> 'Bearer '.$dsa->oauth_token.' '
            ],
                'json' => $validatedData
        ];
        $url = 'https://api.spotify.com/v1/users/'.$dsa->provider_id.'/playlists';

        $client = new Client();

        $response =  $client->post($url, $data);

        $content = json_decode($response->getBody()->getContents());


        if(!$content){
            return response()->json(
                [
                    'error' => true,
                    'data' => [
                        'playlist' => $content,
                    ]
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

      $user->createPlaylist($content->id, $content->images, $content->name, $content->description, $dsa->provider);

        return response()->json(
            [
                'error' => false,
                'data' => [
                    'playlist' => $content,
                ]
            ],
            Response::HTTP_OK
        );

    }

    public static function addPlaylistTracks(Request $request)
    {
        /** @var \App\User $user */

        $user = Auth::user();
        $dsa = $user->spotifyDsa();
        $id = $request->route('id');
        $dsaPlaylist = DsaPlaylist::find($id);

        $validatedData = $request->validate([
            'uris' => 'string',
            'position' => 'numeric',
        ]);

        $data = [
            'headers' => [
                'Content-Type' => 'application/json',
                'Authorization'=> 'Bearer '.$dsa->oauth_token.' '
            ],
            'json' => $validatedData
        ];
        $url = 'https://api.spotify.com/v1/playlists/'.$dsaPlaylist->dsa_playlist_id.'/tracks';

        $client = new Client();

        $response =  $client->post($url, $data);

        $content = $response->getBody()->getContents();


        if(!$content){
            return response()->json(
                [
                    'error' => true,
                    'data' => [
                        'playlist' => $content,
                    ]
                ],
                Response::HTTP_BAD_REQUEST
            );
        }

        $user->addPlaylistTrack($dsaPlaylist->id, $content->snapshot_id);

        return response()->json(
            [
                'error' => false,
                'data' => [
                    'playlist' => $content,
                ]
            ],
            Response::HTTP_OK
        );

    }

}

