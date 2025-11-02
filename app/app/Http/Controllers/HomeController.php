<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class HomeController extends Controller
{
    public function index()
    {
        // minimal data to the view
        $data = [];
        $data['title'] = 'Meal Planning';
        return view('home', $data);
    }

    public function health()
    {
        // raw SQL check
        $ok = false;
        $error = '';
        try {
            $rows = DB::select('SELECT 1 AS ok');
            if (!empty($rows) && isset($rows[0]->ok) && $rows[0]->ok == 1) {
                $ok = true;
            }
        } catch (\Throwable $e) {
            $error = $e->getMessage();
        }

        // plain text response to keep it simple
        if ($ok === true) {
            return response("DB OK\n", 200)->header('Content-Type', 'text/plain');
        } else {
            return response("DB ERROR: " . $error . "\n", 500)->header('Content-Type', 'text/plain');
        }
    }
}
