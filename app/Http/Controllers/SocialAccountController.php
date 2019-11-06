<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Services\SocialAccountService;
use Socialite;
use Illuminate\Support\Facades\Auth;

use Illuminate\Http\Request;

class SocialAccountController extends Controller
{
    public function redirectToProvider($provider)
    {
        return Socialite::driver($provider)->redirect();
    }

    public function handleProviderCallback(SocialAccountService $service, $provider)
    {
        $user = $service->createOrGetUser(Socialite::driver($provider)->stateless());
        Auth::login($user);

        return redirect()->to('/');
    }
    public function logout()
    {
        auth()->logout();
        return redirect()->to('/');
    }
}
