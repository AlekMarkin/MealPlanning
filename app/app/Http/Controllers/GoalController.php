<?php

namespace App\Http\Controllers;

use App\Models\Goal;
use Illuminate\Http\Request;

/*
manages nutritional goals for authenticated users,
handles CRUD operations for daily, weekly, and monthly
targets across various nutritional metrics
*/
class GoalController extends Controller
{
    /*
    retrieves and displays all goals for the authenticated user,
    goals are ordered by metric name for consistent presentation
    */
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
            'metrics' => Goal::metrics(),
        ]);
    }

    //displays the form for creating a new nutritional goal
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

    /*
    validates and stores a new goal in the database,
    enforces uniqueness constraint: one goal per user, metric, and period combination
    */
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
            'period'       => 'required|in:daily,weekly,monthly',
        ]);

        //checks for existing goal with same metric and period
        $exists = Goal::where('user_id', $userId)
            ->where('metric', $data['metric'])
            ->where('period', $data['period'])
            ->exists();

        if ($exists) {
            return back()->withInput()->with('error', 'Goal for this metric and period already exists.');
        }

        $data['user_id'] = $userId;
        Goal::create($data);

        return redirect('/goals')->with('success', 'Goal saved.');
    }

    /*
    displays the form for editing an existing goal,
    verifies ownership before allowing access to the edit form
    */
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

    /*
    validates and updates an existing goal in the database,
    checks for conflicts with other goals having the same metric and period
    */
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
            'period'       => 'required|in:daily,weekly,monthly',
        ]);

        //checks for conflict with another goal
        $conflict = Goal::where('user_id', $userId)
            ->where('metric', $data['metric'])
            ->where('period', $data['period'])
            ->where('id', '!=', $goal->id)
            ->exists();

        if ($conflict) {
            return back()->withInput()->with('error', 'Another goal with this metric and period already exists.');
        }

        $goal->update($data);

        return redirect('/goals')->with('success', 'Goal updated.');
    }

    //deletes a goal after verifying ownership
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