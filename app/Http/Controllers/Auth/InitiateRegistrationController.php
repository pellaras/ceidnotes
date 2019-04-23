<?php

namespace App\Http\Controllers\Auth;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Jobs\SendVerificationEmail;

class InitiateRegistrationController extends Controller
{
    public function create()
    {
        return view('auth.register-initiate');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username' => ['required', 'regex:/^[a-z]+$/', 'min:3', 'max:32', 'unique:users'],
        ]);

        SendVerificationEmail::dispatch($data['username']);

        return redirect()->back()->with('success', 'Το email με τις οδηγίες έχει αποσταλεί στο λογαριασμό <strong>' . $data['username'] . '@ceid.upatras.gr</strong>');
    }
}
