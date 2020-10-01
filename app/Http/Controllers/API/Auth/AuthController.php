<?php

namespace App\Http\Controllers\API\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
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
        if($provider === 'spotify'){
            return Socialite::driver($provider)->scopes([
                'user-library-modify',
                'playlist-modify-public',
                'user-read-email',
                'streaming',
                'user-library-read',
                'user-modify-playback-state',
                'user-read-playback-state',
                'playlist-read-private',
                'user-read-currently-playing',
                'user-read-recently-played',
                'app-remote-control',
                'user-read-private'
            ])->redirect();
        }
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


        Auth::login($user);

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
        Auth::login($user);

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

        Auth::login($user);

        $user->createDsaAccount($provider, $socialUser->getId(), $socialUser->token, $socialUser->refreshToken);

        return $user;
    }

    protected function registerSocialUser(Request $request)
    {
        $validateData = $request->validate([
            'email' => 'required|string|unique:users|max:255|email:rfc,dns',
            'password' => 'required|string|min:6'
        ]);

        if( User::where('email', '=', $validateData['email'])->first())
        {
//            return redirect()->back()->withInput()->withErrors(['email' =>  'Email already exist']);
            return response()->json(['error' => 'Email already exist.'], 400);
        }

        /** @var \App\User $user */
        $user = User::create([
            'email'=> $validateData['email'],
            'password' => Hash::make($validateData['password'], [
                'rounds' => 12,
            ])
        ]);

        Auth::login($user);

        return response()->json(
            [
                'error' => false,
                'data' => $user
            ],
            Response::HTTP_OK
        );
    }
    protected function loginUser(Request $request)
    {
        $validateData = $request->validate([
            'email' => 'required|string|unique:users|max:255|email:rfc,dns',
            'password' => 'required|string|min:6'
        ]);

        $user = User::where('email', '=', $validateData['email'])->first();
        if(!$user && !Hash::check($validateData['password'], $user->password)){
//            return redirect()->back()->withInput()->withErrors(['Msg',  'Invalid login credentials']);
            return response()->json(['error' => 'Invalid login credentials.'], 400);
        };

        Auth::login($user);

        return response()->json(
            [
                'error' => false,
                'data' => $user
            ],
            Response::HTTP_OK
        );
    }

    protected function getUser(Request $request)
    {
            /** @var \App\User $user */
        $userId = $request->route('id');
            $user = User::findOrFail($userId);
            if(!$user){
//                return redirect()->back()->withErrors(['Msg',  'User not found']);
                return response()->json(['error' => 'User not found.'], 403);
            }

            return response()->json(
                [
                    'error' => false,
                    'data' => $user
                ],
                Response::HTTP_OK
            );
    }


}
