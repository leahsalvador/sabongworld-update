<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;

// use Illuminate\Foundation\Auth;
use Illuminate\Support\Facades\Auth;


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
    // protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    //  protected function redirectTo()
    // {
    //     $user=Auth::user();

    //   dd($user);
    //     if ( $user->user_level == 'admin' ) {// do your magic here
    //         return redirect()->route('admin');
    //     }else if ( $user->user_level == 'agent') {
    //         # code...
    //         return redirect()->route('agent');
    //     }else{
    //         return redirect()->route('player');
    //     }

    // }
    protected function authenticated()
    {
        // dd($user);
        $user = Auth::user();
        if (!Auth::user()) {
            return view('welcome');
        }
        Auth::logoutOtherDevices(request('password'));
        // auth()->logoutOtherDevices();

        if ($user->user_level == 'admin') {// do your magic here
            return redirect()->route('admin-dashboard');
        } else if ($user->user_level == 'master-agent') {
            # code...
            return redirect()->route('dashboard');
        } else if ($user->user_level == 'sub-agent') {
            # code...
            return redirect()->route('dashboard');
        } else if ($user->user_level == 'gold-agent') {
            # code...
            return redirect()->route('dashboard');
        } else if ($user->user_level == 'bronze-agent') {
            # code...
            return redirect()->route('dashboard');
        } else if ($user->user_level == 'silver-agent') {
            # code...
            return redirect()->route('dashboard');
        } else if ($user->user_level == 'super-admin') {
            # code...
            return redirect()->route('superadmin');
        } else {
            return redirect()->route('player');
        }
    }

    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    public function username()
    {
        return 'username';
    }

    public function showLoginForm()
    {
        $user = Auth::user();
        if (!$user) {
            return view('welcome');
        }
        if ($user->user_level == 'admin') {// do your magic here
            return redirect()->route('admin-dashboard');
        } else if ($user->user_level == 'master-agent') {
            # code...
            return redirect()->route('dashboard');
        } else if ($user->user_level == 'sub-agent') {
            # code...
            return redirect()->route('dashboard');
        } else if ($user->user_level == 'gold-agent') {
            # code...
            return redirect()->route('dashboard');
        } else if ($user->user_level == 'silver-agent') {
            # code...
            return redirect()->route('dashboard');
        } else if ($user->user_level == 'bronze-agent') {
            # code...
            return redirect()->route('dashboard');
        } else {
            return redirect()->route('player');
        }
    }
    // public function login(Request $request)
    // {
    //     $input = $request->all();

    //     $this->validate($request, [
    //         'username' => 'required',
    //         'password' => 'required',
    //     ]);

    //     $fieldType = filter_var($request->username, FILTER_VALIDATE_EMAIL) ? 'email' : 'username';
    //     if(auth()->attempt(array($fieldType => $input['username'], 'password' => $input['password'])))
    //     {
    //         return redirect()->route('home');
    //     }else{
    //         return redirect()->route('login')
    //             ->with('error','Email-Address And Password Are Wrong.');
    //     }

    // }
}
