<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/*
manages health metrics tracking for authenticated users,
handles recording and analysis of weight and blood pressure measurements
with historical data retrieval and statistical calculations
*/
class UserMetricsController extends Controller
{
    /*
    displays the user's health metrics dashboard,
    shows all recorded metrics, the most recent reading,
    and calculates 30-day rolling averages for trend analysis
    */
    public function index()
    {
        if (!session('user_id')) {
            return redirect()->route('home')->with('error', 'Please log in first');
        }

        $userId = session('user_id');

        //retrieves metrics ordered by date descending for table display
        $metrics = DB::table('user_metrics')
            ->where('user_id', $userId)
            ->orderBy('recorded_date', 'desc')
            ->get();

        $latestMetric = $metrics->first();

        //calculates 30-day rolling averages
        $thirtyDaysAgo = date('Y-m-d', strtotime('-30 days'));
        $recentMetrics = DB::table('user_metrics')
            ->where('user_id', $userId)
            ->where('recorded_date', '>=', $thirtyDaysAgo)
            ->get();

        $averages = [
            'weight' => $recentMetrics->avg('weight_kg'),
            'systolic' => $recentMetrics->avg('bp_systolic'),
            'diastolic' => $recentMetrics->avg('bp_diastolic'),
        ];

        //retrieves metrics ordered by date ascending for chart display
        $chartData = DB::table('user_metrics')
            ->where('user_id', $userId)
            ->orderBy('recorded_date', 'asc')
            ->get();

        return view('user-metrics.index', compact('metrics', 'latestMetric', 'averages', 'chartData'));
    }

    //displays the form for recording a new health metric entry
    public function create()
    {
        if (!session('user_id')) {
            return redirect()->route('home')->with('error', 'Please log in first');
        }

        return view('user-metrics.create');
    }

    /*
    validates and stores a new health metric entry in the database,
    prevents duplicate entries for the same date to ensure data integrity
    */
    public function store(Request $request)
    {
        if (!session('user_id')) {
            return redirect()->route('home')->with('error', 'Please log in first');
        }

        $data = $request->validate([
            'recorded_date' => ['required', 'date'],
            'weight_kg' => ['required', 'numeric', 'min:0', 'max:500'],
            'bp_systolic' => ['required', 'integer', 'min:50', 'max:300'],
            'bp_diastolic' => ['required', 'integer', 'min:30', 'max:200'],
        ]);

        $userId = session('user_id');

        //checks if metric already exists for this date
        $exists = DB::table('user_metrics')
            ->where('user_id', $userId)
            ->where('recorded_date', $data['recorded_date'])
            ->exists();

        if ($exists) {
            return back()
                ->with('error', 'You already have a metric recorded for this date')
                ->withInput();
        }

        DB::table('user_metrics')->insert([
            'user_id' => $userId,
            'recorded_date' => $data['recorded_date'],
            'weight_kg' => $data['weight_kg'],
            'bp_systolic' => $data['bp_systolic'],
            'bp_diastolic' => $data['bp_diastolic'],
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('user-metrics.index')
            ->with('success', 'Metric added successfully!');
    }

    /*
    displays the form for editing an existing health metric entry,
    verifies ownership before allowing access to the edit form
    */
    public function edit($id)
    {
        if (!session('user_id')) {
            return redirect()->route('home')->with('error', 'Please log in first');
        }

        $metric = DB::table('user_metrics')
            ->where('id', $id)
            ->where('user_id', session('user_id'))
            ->first();

        if (!$metric) {
            return redirect()->route('user-metrics.index')
                ->with('error', 'Metric not found');
        }

        return view('user-metrics.edit', compact('metric'));
    }

    /*
    validates and updates an existing health metric entry,
    prevents date conflicts with other entries for the same user
    */
    public function update(Request $request, $id)
    {
        if (!session('user_id')) {
            return redirect()->route('home')->with('error', 'Please log in first');
        }

        $data = $request->validate([
            'recorded_date' => ['required', 'date'],
            'weight_kg' => ['required', 'numeric', 'min:0', 'max:500'],
            'bp_systolic' => ['required', 'integer', 'min:50', 'max:300'],
            'bp_diastolic' => ['required', 'integer', 'min:30', 'max:200'],
        ]);

        $metric = DB::table('user_metrics')
            ->where('id', $id)
            ->where('user_id', session('user_id'))
            ->first();

        if (!$metric) {
            return redirect()->route('user-metrics.index')
                ->with('error', 'Metric not found');
        }

        //checks if another metric exists for this date
        $exists = DB::table('user_metrics')
            ->where('user_id', session('user_id'))
            ->where('recorded_date', $data['recorded_date'])
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()
                ->with('error', 'You already have a metric recorded for this date')
                ->withInput();
        }

        DB::table('user_metrics')
            ->where('id', $id)
            ->update([
                'recorded_date' => $data['recorded_date'],
                'weight_kg' => $data['weight_kg'],
                'bp_systolic' => $data['bp_systolic'],
                'bp_diastolic' => $data['bp_diastolic'],
                'updated_at' => now(),
            ]);

        return redirect()->route('user-metrics.index')
            ->with('success', 'Metric updated successfully!');
    }

    //deletes a health metric entry after verifying ownership
    public function destroy($id)
    {
        if (!session('user_id')) {
            return redirect()->route('home')->with('error', 'Please log in first');
        }

        $deleted = DB::table('user_metrics')
            ->where('id', $id)
            ->where('user_id', session('user_id'))
            ->delete();

        if (!$deleted) {
            return redirect()->route('user-metrics.index')
                ->with('error', 'Metric not found');
        }

        return redirect()->route('user-metrics.index')
            ->with('success', 'Metric deleted successfully!');
    }
}