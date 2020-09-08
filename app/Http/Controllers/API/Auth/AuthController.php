<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    

    /**
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function appleLogin(Request $request)
    {
        $provider = 'apple';
        $token = $request->token;

        $socialUser = Socialite::driver($provider)->userFromToken($token);
        $user = $this->getLocalUser($socialUser);

        $client = DB::table('oauth_clients')
            ->where('password_client', true)
            ->first();
        if (!$client) {
            return response()->json([
                'message' => trans('validation.passport.client_error'),
                'status' => Response::HTTP_INTERNAL_SERVER_ERROR
            ], Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        $data = [
            'grant_type' => 'social',
            'client_id' => $client->id,
            'client_secret' => $client->secret,
            'provider' => 'apple',
            'access_token' => $token
        ];
        $request = Request::create('/oauth/token', 'POST', $data);

        $content = json_decode(app()->handle($request)->getContent());
        if (isset($content->error) && $content->error === 'invalid_request') {
            return response()->json(['error' => true, 'message' => $content->message]);
        }

        return response()->json(
            [
                'error' => false,
                'data' => [
                    'user' => $user,
                    'meta' => [
                        'token' => $content->access_token,
                        'expired_at' => $content->expires_in,
                        'refresh_token' => $content->refresh_token,
                        'type' => 'Bearer'
                    ],
                ]
            ],
            Response::HTTP_OK
        );
    }

    /**
     * @param  OAuthTwoUser  $socialUser
     * @return User|null
     */
    protected function getLocalUser(OAuthTwoUser $socialUser): ?User
    {
        $user = User::where('email', $socialUser->email)->first();

        if (!$user) {
            $user = $this->registerAppleUser($socialUser);
        }

        return $user;
    }


    /**
     * @param  OAuthTwoUser  $socialUser
     * @return User|null
     */
    protected function registerAppleUser(OAuthTwoUser $socialUser): ?User
    {
       $user = User::create(
            [
                'full_name' => request()->fullName ? request()->fullName : 'Apple User',
                'email' => $socialUser->email,
                'password' => Str::random(30), // Social users are password-less
                
            ]
        );
        return $user;
    }
}
