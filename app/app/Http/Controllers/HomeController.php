<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $userId = session('user_id');

        // Guest view (slogan + login/register)
        if (!$userId) {
            return view('home', [
                'mode' => 'guest',
                'goals' => [],
            ]);
        }

        // Authenticated dashboard (quick links + latest goals)
        $goals = DB::select(
            'SELECT id, name, direction, target_value 
             FROM goals 
             WHERE user_id = ? 
             ORDER BY id DESC 
             LIMIT 5',
            [(int)$userId]
        );

        return view('home', [
            'mode' => 'auth',
            'goals' => $goals,
        ]);
    }
}
