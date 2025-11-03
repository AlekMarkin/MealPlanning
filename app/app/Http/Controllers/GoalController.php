<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class GoalController extends Controller
{
    private function requireLogin()
    {
        if (!session('user_id')) {
            return redirect('/login')->with('error', 'Please sign in first.');
        }
        return null;
    }

    public function index(Request $request)
    {
        $redirect = $this->requireLogin();
        if ($redirect) { return $redirect; }

        $userId = (int) session('user_id');

        // fetch only current user's goals
        $goals = DB::select('SELECT id, name, direction, target_value FROM goals WHERE user_id = ? ORDER BY id DESC', [$userId]);

        return view('goals.index', ['goals' => $goals]);
    }

    public function create(Request $request)
    {
        $redirect = $this->requireLogin();
        if ($redirect) { return $redirect; }

        return view('goals.create');
    }

    public function store(Request $request)
    {
        $redirect = $this->requireLogin();
        if ($redirect) { return $redirect; }

        $name = trim($request->input('name'));
        $direction = trim($request->input('direction'));
        $target = $request->input('target_value');

        // validation
        if ($name === '') {
            return back()->with('error', 'Name is required.')->withInput();
        }
        if ($direction !== 'up' && $direction !== 'down') {
            return back()->with('error', 'Direction must be up or down.')->withInput();
        }
        if (!is_numeric($target)) {
            return back()->with('error', 'Target must be a number.')->withInput();
        }

        $userId = (int) session('user_id');

        // insert
        DB::insert(
            'INSERT INTO goals (user_id, name, direction, target_value, created_at, updated_at) VALUES (?, ?, ?, ?, NOW(), NOW())',
            [$userId, $name, $direction, (int)$target]
        );

        return redirect('/goals')->with('success', 'Goal created.');
    }

    public function edit($id)
    {
        $redirect = $this->requireLogin();
        if ($redirect) { return $redirect; }

        $userId = (int) session('user_id');

        $goal = DB::selectOne('SELECT id, name, direction, target_value FROM goals WHERE id = ? AND user_id = ?', [(int)$id, $userId]);
        if (!$goal) {
            return redirect('/goals')->with('error', 'Goal not found.');
        }

        return view('goals.edit', ['goal' => $goal]);
    }

    public function update(Request $request, $id)
    {
        $redirect = $this->requireLogin();
        if ($redirect) { return $redirect; }

        $name = trim($request->input('name'));
        $direction = trim($request->input('direction'));
        $target = $request->input('target_value');

        if ($name === '') {
            return back()->with('error', 'Name is required.');
        }
        if ($direction !== 'up' && $direction !== 'down') {
            return back()->with('error', 'Direction must be up or down.');
        }
        if (!is_numeric($target)) {
            return back()->with('error', 'Target must be a number.');
        }

        $userId = (int) session('user_id');

        $rows = DB::update(
            'UPDATE goals SET name = ?, direction = ?, target_value = ?, updated_at = NOW() WHERE id = ? AND user_id = ?',
            [$name, $direction, (int)$target, (int)$id, $userId]
        );

        if ($rows < 1) {
            return redirect('/goals')->with('error', 'Nothing updated or not your goal.');
        }

        return redirect('/goals')->with('success', 'Goal updated.');
    }

    public function destroy(Request $request, $id)
    {
        $redirect = $this->requireLogin();
        if ($redirect) { return $redirect; }

        $userId = (int) session('user_id');

        $rows = DB::delete('DELETE FROM goals WHERE id = ? AND user_id = ?', [(int)$id, $userId]);

        if ($rows < 1) {
            return redirect('/goals')->with('error', 'Not deleted (not found or not yours).');
        }

        return redirect('/goals')->with('success', 'Goal deleted.');
    }
}
