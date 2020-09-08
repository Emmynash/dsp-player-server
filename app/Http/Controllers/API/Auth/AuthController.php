<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class AuthController extends Controller
{

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @return \Illuminate\Http\Response
     */
    public function redirectToProvider(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * @param  Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function handleProviderCallback(string $provider)
    {
        $socialUser = Socialite::driver($provider)->userFromToken($token);
        $token = $socialUser->token;
        $user = $this->getLocalUser($socialUser);

        $authToken = $user->createToken('Token Name')->accessToken;
        
        if (isset($content->error) && $content->error === 'invalid_request') {
            return response()->json(['error' => true, 'message' => $content->message]);
        }

        return response()->json(
            [
                'error' => false,
                'data' => [
                    'user' => $user,
                    'meta' => [
                        'token' => $authToken->access_token,
                        'expired_at' => $authToken->expires_in,
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
        $user = User::where('email', $socialUser->getEmail())->first();

        if (!$user) {
            $user = $this->registerUser($socialUser);
        }

        return $user;
    }


    /**
     * @param  OAuthTwoUser  $socialUser
     * @return User|null
     */
    protected function registerUser(OAuthTwoUser $socialUser): ?User
    {
       $user = User::create(
            [
                'name' => $socialUser->getName() ? $socialUser->getName() : 'Apple User',
                'email' => $socialUser->getEmail(),
                'password' => Str::random(30), // Social users are password-less
                
            ]
        );
        return $user;
    }
}
