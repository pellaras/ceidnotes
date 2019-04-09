<?php

namespace App\Http\Controllers\Auth;

use App\User;
use GuzzleHttp\Client;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Auth\Events\Registered;
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
    protected $redirectTo = '/home';

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
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data)
    {
        return Validator::make($data, [
            // 'name' => 'required|string|max:255',
            // 'email' => 'required|string|email|max:255|unique:users',
            'username' => 'required|string|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);
    }

    public function register(Request $request)
    {
        $this->validator($request->all())->validate();

        event(new Registered($user = $this->create($request->all())));

        // $this->guard()->login($user);

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
        $additional_data = $this->getData($data['username']);

        if (! $additional_data) {
            dd('failed');
        }

        return User::create([
            'name' => $additional_data['name'],
            'username' => $data['username'],
            'email' => $data['username'] . '@ceid.upatras.gr',
            'password' => bcrypt($data['password']),
            'AM' => $additional_data['AM'],
            'registration_year' => $additional_data['registration_year'],
        ]);
    }

    protected function getData($username)
    {
        $client = new Client([
            'form_params' => [
                'attribute' => 'uid',
                'criterion' => '=',
                'keyword' => $username,
                'dn' => 'cn=users,cn=accounts,dc=ceid,dc=upatras,dc=gr',
                'search' => 'search',
            ],
            'verify' => false
        ]);

        $response = $client->post('https://directory.ceid.upatras.gr/');

        if ($response->getStatusCode() !== 200) {
            return null;
        }

        $contents = $response->getBody()->getContents();

        $regex = '/<td class="n">\s*'
                        .'<span class="given-name">(.*)<\/span>\s*'
                        .'<span class="family-name">(.*)<\/span>\s*'
                        .'<\/td>\s*'
                        .'<td class="fn">(.*)<\/td>\s*'
                        .'<td class="org">CEID<\/td>\s*'
                        .'<!-- <td class="title"><\/td> -->\s*'
                        .'<td class="title">(.*)<\/td>\s*'
                        .'<td class="tel">([0-9]+)\/([0-9]+)<\/td>\s*'
                        .'<td class="email"><a href="mailto:'.$username.'@ceid.upatras.gr">'.$username.'@ceid.upatras.gr<\/a><\/td>/';

        if (! preg_match($regex, $contents, $parsed_data)) {
            return null;
        }

        $user_data = [
            'name' => $parsed_data[3],
            'AM' => $parsed_data[5],
            'registration_year' => $parsed_data[6],
        ];

        return $user_data;
    }
}
