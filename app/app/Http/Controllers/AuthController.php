<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

//handles user authentication including login, registration, and logout
class AuthController extends Controller
{
    /*
    authenticates user credentials and establishes a session,
    validates email and password, checks against database records,
    and stores user information in the session upon successful login.
    */
    public function login(Request $request)
    {
        $data = $request->validate([
            'email'    => ['required', 'email'],
            'password' => ['required'],
        ]);

        $user = DB::table('users')->where('email', $data['email'])->first();

        if (!$user || !Hash::check($data['password'], $user->password)) {
            return back()
                ->with('error', 'Invalid email or password')
                ->withInput(['email' => $data['email']]);
        }

        //stores user data in session
        $request->session()->put([
            'user_id'    => $user->id,
            'user_name'  => $user->name,
            'user_email' => $user->email,
        ]);

        return redirect()->route('home')->with('success', 'Welcome back!');
    }

    /*
    creates a new user account and establishes a session,
    validates registration data, checks for duplicate emails,
    hashes the password, and stores user information in the session
    */
    public function register(Request $request)
    {
        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'email'    => ['required', 'email'],
            'password' => ['required', 'min:6', 'confirmed'],
        ]);

        //prevents duplicate email registration
        if (DB::table('users')->where('email', $data['email'])->exists()) {
            return back()
                ->with('error', 'That email is already registered')
                ->withInput(['name' => $data['name'], 'email' => $data['email']]);
        }

        $id = DB::table('users')->insertGetId([
            'name'     => $data['name'],
            'email'    => $data['email'],
            'password' => Hash::make($data['password']),
        ]);

        //stores user data in session
        $request->session()->put([
            'user_id'    => $id,
            'user_name'  => $data['name'],
            'user_email' => $data['email'],
        ]);

        return redirect()->route('home')->with('success', 'Account created!');
    }

    /*
    terminates the user session and regenerates the CSRF token,
    invalidates all session data and redirects to the home page
    */
    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logged out');
    }
}