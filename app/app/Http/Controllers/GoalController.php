<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;

class GoalController extends Controller
{
    public function index(Request $request)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect('/')->with('error', 'Please sign in.');
        }

        $goals = Goal::where('user_id', $userId)
            ->orderBy('metric')
            ->get();

        return view('goals.index', [
            'goals'   => $goals,
            'metrics' => Goal::metrics(), // dropdown options
        ]);
    }

    public function create()
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect('/')->with('error', 'Please sign in.');
        }

        return view('goals.create', [
            'metrics' => Goal::metrics(),
        ]);
    }

    public function store(Request $request)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect('/')->with('error', 'Please sign in.');
        }

        $validMetrics = array_keys(Goal::metrics());

        $data = $request->validate([
            'metric'       => 'required|in:' . implode(',', $validMetrics),
            'target_value' => 'required|numeric|min:0',
        ]);

        // enforce 1 per user+metric
        $exists = Goal::where('user_id', $userId)
            ->where('metric', $data['metric'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Goal for this metric already exists.');
        }

        $data['user_id'] = $userId;
        Goal::create($data);

        return redirect('/goals')->with('success', 'Goal saved.');
    }

    public function edit($id)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect('/')->with('error', 'Please sign in.');
        }

        $goal = Goal::where('user_id', $userId)->findOrFail($id);

        return view('goals.edit', [
            'goal'    => $goal,
            'metrics' => Goal::metrics(),
        ]);
    }

    public function update(Request $request, $id)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect('/')->with('error', 'Please sign in.');
        }

        $goal = Goal::where('user_id', $userId)->findOrFail($id);

        $validMetrics = array_keys(Goal::metrics());

        $data = $request->validate([
            'metric'       => 'required|in:' . implode(',', $validMetrics),
            'target_value' => 'required|numeric|min:0',
        ]);

        // prevent conflicts with others (same user+metric)
        $conflict = Goal::where('user_id', $userId)
            ->where('metric', $data['metric'])
            ->where('id', '!=', $goal->id)
            ->exists();

        if ($conflict) {
            return back()->withInput()->with('error', 'Another goal with this metric already exists.');
        }

        $goal->update($data);

        return redirect('/goals')->with('success', 'Goal updated.');
    }

    public function destroy($id)
    {
        $userId = session('user_id');
        if (!$userId) {
            return redirect('/')->with('error', 'Please sign in.');
        }

        $goal = Goal::where('user_id', $userId)->findOrFail($id);
        $goal->delete();

        return redirect('/goals')->with('success', 'Goal removed.');
    }
}
