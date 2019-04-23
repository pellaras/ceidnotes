<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Foundation\Auth\RegistersUsers;

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
    protected $redirectTo = '/';

    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('guest');
    }

    public function showRegistrationForm($username)
    {
        if (User::where('username', $username)->count()) {
            return redirect('/');
        }

        return view('auth.register', compact('username'));
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            'password' => ['required', 'string', 'min:8', 'confirmed'],
        ]);
    }

    public function register(Request $request, $username)
    {
        if (User::where('username', $username)->count()) {
            return redirect('/');
        }

        $data = $this->validator($request->all())->validate();

        $user_info = User::getDirectoryData($username);

        if (! $user_info) {
            return back()->withErrors(['global' => 'Σφάλμα κατά την φόρτωση των πληροφοριών του χρήστη. Παρακαλώ δοκιμάστε αργότερα.']);
        }

        $data['username'] = $username;
        $data['name'] = $user_info['name'];
        $data['AM'] = $user_info['AM'];
        $data['registration_year'] = $user_info['registration_year'];

        event(new Registered($user = $this->create($data)));

        $this->guard()->login($user);

        return redirect('/');
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return \App\User
     */
    protected function create(array $data)
    {
        return User::create([
            'name' => $data['name'],
            'username' => $data['username'],
            'email' => $data['username'] . '@ceid.upatras.gr',
            'password' => Hash::make($data['password']),
            'AM' => $data['AM'],
            'registration_year' => $data['registration_year'],
        ]);
    }
}
