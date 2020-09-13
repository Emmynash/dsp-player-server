<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Laravel\Socialite\Facades\Socialite;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Laravel\Socialite\Two\User as OAuthTwoUser;
use App\User;
use Illuminate\Support\Str;

class AuthController extends Controller
{

    /**
     * Redirect the user to the GitHub authentication page.
     *
     * @param string $provider
     * @return RedirectResponse
     */
    public function redirectToProvider(string $provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    /**
     * @param string $provider
     * @param Request $request
     * @return JsonResponse
     */
    public function handleProviderCallback(string $provider, Request $request)
    {
        $socialUser =  Socialite::driver($provider)->stateless()->user();
        $user = $this->getLocalUser($socialUser, $provider);

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
                        'token' => $authToken,
//                        'expired_at' => $authToken->expires_in,
                        'type' => 'Bearer'
                    ],
                ]
            ],
            Response::HTTP_OK
        );
    }

    /**
     * @param OAuthTwoUser $socialUser
     * @param string $provider
     * @return User|null
     */
    protected function getLocalUser(OAuthTwoUser $socialUser, string $provider): ?User
    {
        $user = User::where('id', $socialUser->getId())->first();

        if (!$user) {
            $user = $this->registerUser($socialUser, $provider);
        }

        return $user;
    }


    /**
     * @param  OAuthTwoUser  $socialUser
     * @return User|null
     */
    protected function registerUser(OAuthTwoUser $socialUser, string $provider): ?User
    {
        /** @var \App\User $user */
        $user = User::create(
             [
                 'name' => $socialUser->getName() ? $socialUser->getName() : 'Social User',
                 'email' => $socialUser->getEmail() ? $socialUser->getEmail() : ''.$socialUser->getId().'@pearljam.com',
                 'password' => Str::random(30), // Social users are password-less
             ]
        );

        $user->createDsaAccount($provider, $socialUser->getId(), $socialUser->token, $socialUser->refreshToken);

        return $user;
    }
}
