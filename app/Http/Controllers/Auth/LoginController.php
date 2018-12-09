<?php

namespace App\Http\Controllers\Auth;

use App\User;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Redirect;

use Illuminate\Http\Request;
use Socialite;
use \Google_Client;
use \Google_Service_People;
use DB;
use Auth;
use Session;

class LoginController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function redirectToProvider()
    {
        return Socialite::driver('google')
                ->scopes(['openid', 'profile', 'email', Google_Service_People::CONTACTS_READONLY])
                ->redirect();
    }

    public function handleProviderCallback(Request $request)
    {
        $user = Socialite::driver('google')->user();

        $authUser = $this->findUSer($user);

        if($authUser == false){

            Session::flash('message', 'This is a message!');
            return Redirect::to('login');

        }else{
            if($authUser->verified == 1){
                Auth::login($authUser, true);
                return Redirect::to('home');
            }else{
                auth()->logout();
                return back()->with('warning', 'You need to confirm your account. We have sent you an activation code, please check your email.');
            }           
        }

    }

    private function findUSer($provideruser)
    {
        $authUser = User::where('email', $provideruser->getEmail())->first();

        if ($authUser){
            return $authUser;
        }else{
            return false;
        }
    }

    // public function authenticated(Request $request, $user)
    // {
    //     if (!$user->verified) {
    //     auth()->logout();
    //     return back()->with('warning', 'You need to confirm your account. We have sent you an activation code, please check your email.');
    //     }
    //     return redirect()->intended($this->redirectPath());
    // }

}
