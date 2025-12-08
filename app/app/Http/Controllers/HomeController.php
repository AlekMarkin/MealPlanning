<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Goal;
use Carbon\Carbon;

/*
handles the main landing page for both guest and authenticated users,
displays authentication forms for guests and a nutritional dashboard
with goal progress tracking for authenticated users
*/
class HomeController extends Controller
{
    /*
    renders the home page based on authentication status,
    for guests: displays login/registration forms,
    for authenticated users: displays daily nutritional totals and goal progress
    */
    public function index(Request $request)
    {
        $userId   = session('user_id');
        $userName = session('user_name');

        //handles guest view with login/register forms
        if (!$userId) {
            $show = $request->input('show');
            if (!$show) {
                if ($request->routeIs('login.form')) {
                    $show = 'login';
                } elseif ($request->routeIs('register.form')) {
                    $show = 'register';
                }
            }

            return view('home', [
                'mode' => 'guest',
                'show' => in_array($show, ['login', 'register']) ? $show : null,
            ]);
        }

        //handles authenticated user dashboard
        $today = Carbon::now()->toDateString();

        //retrieves daily goals for the user
        $rawGoals = DB::table('goals')
            ->select('metric', 'target_value')
            ->where('user_id', $userId)
            ->where('period', 'daily')
            ->get();

        //maps goals with their metadata
        $metricMeta = Goal::metrics();
        $goals = [];
        foreach ($rawGoals as $g) {
            $meta = $metricMeta[$g->metric] ?? ['label' => $g->metric, 'unit' => ''];
            $goals[$g->metric] = [
                'target' => (float) $g->target_value,
                'label'  => is_array($meta) ? ($meta['label'] ?? $g->metric) : (string) $meta,
                'unit'   => is_array($meta) ? ($meta['unit']  ?? '') : '',
            ];
        }

        //calculates today's nutritional totals
        $totals = $this->computeTodayTotals($userId, $today);

        //generates advice based on goal progress
        $advice = [];
        foreach ($goals as $metric => $g) {
            $intake = (float) ($totals[$metric] ?? 0.0);
            $advice[$metric] = $intake <= $g['target']
                ? "Nice! You're on track - the planet (and future you) will thank you."
                : 'A bit over your target. Tiny tweaks today can make a greener tomorrow.';
        }

        return view('home', [
            'mode'     => 'auth',
            'userName' => $userName,
            'goals'    => $goals,
            'totals'   => $totals,
            'advice'   => $advice,
        ]);
    }

    /*
    calculates the total nutritional values for a user on a specific date,
    aggregates values from both individual food entries and recipe servings,
    scaling food nutrients per 100g and recipe nutrients per serving
    */
    private function computeTodayTotals(int $userId, string $date): array
    {
        $totals = [
            'calories' => 0, 'protein' => 0, 'carbs' => 0, 'fat' => 0,
            'fiber' => 0, 'sugar' => 0, 'sodium' => 0, 'carbon_footprint' => 0,
        ];

        $intakes = DB::table('intakes')
            ->where('user_id', $userId)
            ->whereDate('intake_date', $date)
            ->orderBy('created_at')
            ->get();

        if ($intakes->isEmpty()) {
            return $totals;
        }

        //maps food database columns to metric keys
        $map = [
            'calories' => 'calories',
            'protein'  => 'protein',
            'carbs'    => 'carbs',
            'fat'      => 'fat',
            'fiber'    => 'fiber',
            'sugar'    => 'sugar',
            'sodium_mg' => 'sodium',
            'carbon_footprint_gco2e' => 'carbon_footprint',
        ];

        foreach ($intakes as $row) {
            //processes individual food entries
            if (!is_null($row->food_id) && (int)$row->food_id > 0) {
                $food = DB::table('foods')->where('id', $row->food_id)->first();
                if ($food) {
                    $factor = max(0, (float)($row->quantity_g ?? 0)) / 100.0;
                    foreach ($map as $foodCol => $key) {
                        $totals[$key] += $factor * (float)($food->{$foodCol} ?? 0);
                    }
                }
            }

            //processes recipe entries with serving calculations
            if (!is_null($row->recipe_id) && (int)$row->recipe_id > 0) {
                $recipe = DB::table('recipes')->where('id', $row->recipe_id)->first();
                $items = DB::table('recipe_items')
                    ->where('recipe_id', $row->recipe_id)
                    ->get();

                if ($items->count()) {
                    $recipeTotal = [
                        'calories' => 0, 'protein' => 0, 'carbs' => 0, 'fat' => 0,
                        'fiber' => 0, 'sugar' => 0, 'sodium' => 0, 'carbon_footprint' => 0,
                    ];

                    foreach ($items as $it) {
                        $qtyG = (float)($it->quantity_g ?? $it->grams ?? 0);
                        if ($qtyG <= 0) continue;

                        $food = DB::table('foods')->where('id', $it->food_id)->first();
                        if (!$food) continue;

                        $factor = $qtyG / 100.0;
                        foreach ($map as $foodCol => $key) {
                            $recipeTotal[$key] += $factor * (float)($food->{$foodCol} ?? 0);
                        }
                    }

                    //calculates nutrition based on servings eaten vs total servings
                    $intakeServings = max(1.0, (float)($row->servings ?? 1));
                    $recipeServings = max(1, (int)($recipe->servings ?? 1));
                    foreach ($recipeTotal as $key => $val) {
                        $totals[$key] += $val * ($intakeServings / $recipeServings);
                    }
                }
            }
        }

        return $totals;
    }
}