<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Providers\RouteServiceProvider;
use App\Models\User;
use App\Models\Registration;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Http\Request;
use Illuminate\Routing\Route;

class RegisterController extends Controller
{
    /*
    |--------------------------------------------------------------------------
    | Register Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles the registration of new users as well as their
    | validation and creation. By default this controller uses a trait to
    | provide this functionality without requiring any additional code.
    |
    */

    use RegistersUsers;

    /**
     * Where to redirect users after registration.
     *
     * @var string
     */
    protected $redirectTo = RouteServiceProvider::HOME;

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param array $data
     * @return \Illuminate\Contracts\Validation\Validator
     */

    protected function index($referral_id)
    {
        $referrer = User::where('code', $referral_id)->first();
        if (!$referrer) {
            abort(404);
        } else {
            return view('auth.register', ['referrer' => $referrer]);
        }
    }

    protected function privacy()
    {
        return view('privacyPolicy');
    }
    // protected function validator(Request $request)
    // {

    //     Validator::make($request->all(), [
    //         'name' => ['required', 'string', 'max:255'],
    //         'username' => ['required', 'string', 'max:255'],
    //         'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
    //         'password' => ['required', 'string', 'min:8', 'confirmed'],
    //         'phone_number' => ['required','min:12'],
    //         'referral_number' => ['required','numeric'],
    //     ])->validate();
    // }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param array $data
     * @return \App\Models\User
     */
    protected function create(Request $request)
    {

        Validator::make($request->all(), [
            'name' => ['required', 'string', 'max:255'],
            'username' => ['required', 'string', 'max:255', 'unique:registrations'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:registrations'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'phone_number' => ['required', 'numeric', 'unique:registrations'],
            'facebook_link' => ['required', 'string'],
        ])->validate();

        $referrer = User::where('code', $request->referral_id)->first();
//        dd($referrer);
        $user_level = 'master-agent-player';
        switch ($referrer->user_level) {
            case 'admin':
                $user_level = 'master-agent';
                break;
            case 'master-agent':
                $user_level = 'master-agent-player';
                break;
            case 'sub-agent':
                $user_level = 'sub-agent-player';
                break;
            case 'gold-agent':
                $user_level = 'gold-agent-player';
                break;
            case 'silver-agent':
                $user_level = 'silver-agent-player';
                break;
            case 'bronze-agent':
                $user_level = 'bronze-agent-player';
                break;
        }

        Registration::create([
            'name' => $request->name,
            'username' => $request->username,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'facebook_link' => $request->facebook_link,
            'referral_id' => $request->referral_id,
            'user_level' => $user_level,
            'password' => Hash::make($request->password),

        ]);
        return redirect()->back()->with('success', 'Waiting for request approval.');
    }
}
