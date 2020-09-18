<?php

namespace App\Http\Controllers;
use App\DsaPlaylist;

use Illuminate\Http\Request;
use Illuminate\Http\Response;

class PlaylistController extends Controller
{
    public function store()
    {
        /** @var \App\User $user */

//        return DsaPlaylist::created()->response;
    }

    public function index()
    {
        $data =  DsaPlaylist::all();
        return response()->json(
            [
                'error' => false,
                'data' => [
                    'playlist' => $data,
                ]
            ],
            Response::HTTP_OK
        );

    }

    public function show($playlist_id)
    {
     $data =   DsaPlaylist::findOrFail($playlist_id);
        return response()->json(
            [
                'error' => false,
                'data' => [
                    'playlist' => $data,
                ]
            ],
            Response::HTTP_OK
        );

    }
}
