<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Socialite;
use Auth;
use Exception;


class FacebookAuthController extends Controller
{
    public function redirectToProvider()
    {
        return Socialite::driver('facebook')->redirect();
    }

    public function handleProviderCallback()
    {
        try {
            $facebookUser = Socialite::driver('facebook')->user();
            $existUser = User::where('email', $facebookUser->email)->first();

            if ($existUser) {
                Auth::loginUsingId($existUser->id);
            } else {
                $user = new User;
                $user->facebook_user_id = $facebookUser->getId();
                $user->name = $facebookUser->getName();
                $user->email = $facebookUser->getEmail();
                $user->password = md5(rand(1, 10000));
                $user->save();
                Auth::loginUsingId($user->id);
            }
            return redirect()->to('/home');
        } catch (Exception $e) {
            return 'error';
        }
    }
}