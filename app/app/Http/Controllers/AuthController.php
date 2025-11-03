<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
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

        session([
            'user_id'    => $user->id,
            'user_name'  => $user->name,
            'user_email' => $user->email,
        ]);

        return redirect()->route('home')->with('success', 'Welcome back!');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name'                  => ['required', 'string', 'max:255'],
            'email'                 => ['required', 'email'],
            'password'              => ['required', 'min:6', 'confirmed'],
        ]);

        // Prevent duplicate emails
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

        session([
            'user_id'    => $id,
            'user_name'  => $data['name'],
            'user_email' => $data['email'],
        ]);

        return redirect()->route('home')->with('success', 'Account created!');
    }

    public function logout(Request $request)
    {
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('home')->with('success', 'Logged out');
    }
}
