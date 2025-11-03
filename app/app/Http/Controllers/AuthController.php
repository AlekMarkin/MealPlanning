<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function home(Request $request)
    {
        $userId = $request->session()->get('user_id');
        $userName = $request->session()->get('user_name');

        return view('home', [
            'userId' => $userId,
            'userName' => $userName,
            'error' => $request->session()->get('error'),
            'success' => $request->session()->get('success'),
            // also show old input for convenience
            'old_email' => $request->session()->get('old_email'),
            'old_name'  => $request->session()->get('old_name'),
        ]);
    }

    public function register(Request $request)
    {
        $name = trim((string)$request->input('name'));
        $email = trim((string)$request->input('email'));
        $password = (string)$request->input('password');

        //validation
        if ($name === '' || $email === '' || $password === '') {
            $request->session()->flash('error', 'Please fill in name, email and password.');
            $request->session()->flash('old_email', $email);
            $request->session()->flash('old_name', $name);
            return redirect()->route('home');
        }

        // check duplicate email 
        $rows = DB::select('SELECT id FROM users WHERE email = ? LIMIT 1', [$email]);
        if (count($rows) > 0) {
            $request->session()->flash('error', 'Email already exists. Please log in instead.');
            $request->session()->flash('old_email', $email);
            $request->session()->flash('old_name', $name);
            return redirect()->route('home');
        }

        // hash password 
        $hash = password_hash($password, PASSWORD_DEFAULT);

        // insert
        DB::insert('INSERT INTO users (name, email, password) VALUES (?, ?, ?)', [$name, $email, $hash]);

        // fetch the user back to store in session
        $user = DB::select('SELECT id, name FROM users WHERE email = ? LIMIT 1', [$email]);
        if (count($user) === 1) {
            $request->session()->put('user_id', $user[0]->id);
            $request->session()->put('user_name', $user[0]->name);
        }

        $request->session()->flash('success', 'Registered and logged in.');
        return redirect()->route('home');
    }

    public function login(Request $request)
    {
        $email = trim((string)$request->input('email'));
        $password = (string)$request->input('password');

        if ($email === '' || $password === '') {
            $request->session()->flash('error', 'Please provide email and password.');
            $request->session()->flash('old_email', $email);
            return redirect()->route('home');
        }

        $rows = DB::select('SELECT id, name, password FROM users WHERE email = ? LIMIT 1', [$email]);
        if (count($rows) !== 1) {
            $request->session()->flash('error', 'User not found.');
            $request->session()->flash('old_email', $email);
            return redirect()->route('home');
        }

        $user = $rows[0];
        if (!password_verify($password, $user->password)) {
            $request->session()->flash('error', 'Wrong password.');
            $request->session()->flash('old_email', $email);
            return redirect()->route('home');
        }

        $request->session()->put('user_id', $user->id);
        $request->session()->put('user_name', $user->name);

        $request->session()->flash('success', 'Logged in successfully.');
        return redirect()->route('home');
    }

    public function logout(Request $request)
    {
      
        $request->session()->forget('user_id');
        $request->session()->forget('user_name');

        $request->session()->flash('success', 'Logged out.');
        return redirect()->route('home');
    }
}
