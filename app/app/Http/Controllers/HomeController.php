<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Goal; // for Goal::metrics()
use Carbon\Carbon;

class HomeController extends Controller
{
    public function index(Request $request)
    {
        $userId   = session('user_id');
        $userName = session('user_name');

        // --- GUEST: show hero + auth forms (decide which form to auto-focus) ---
        if (!$userId) {
            // allow both routes (/login, /register) and query ?show=login|register
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

        // --- AUTH: compute today's totals and load user's goals ---
        $today = Carbon::now()->toDateString();

        // goals as [metric => ['target'=>float,'label'=>..., 'unit'=>...]]
        $rawGoals = DB::table('goals')
            ->select('metric', 'target_value')
            ->where('user_id', $userId)
            ->get();

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

        // totals for today
        $totals = $this->computeTodayTotals($userId, $today);

        // simple advice: assume target is an upper limit
        $advice = [];
        foreach ($goals as $metric => $g) {
            $intake = (float) ($totals[$metric] ?? 0.0);
            $advice[$metric] = $intake <= $g['target']
                ? 'Nice! You’re on track — the planet (and future you) will thank you.'
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

    /**
     * Aggregate today's totals over foods and recipes.
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

        // Map Food columns -> metric keys
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
            // FOOD: scale per 100g
            if (!is_null($row->food_id) && (int)$row->food_id > 0) {
                $food = DB::table('foods')->where('id', $row->food_id)->first();
                if ($food) {
                    $factor = max(0, (float)($row->quantity_g ?? 0)) / 100.0;
                    foreach ($map as $foodCol => $key) {
                        $totals[$key] += $factor * (float)($food->{$foodCol} ?? 0);
                    }
                }
            }

            // RECIPE: sum items once, then multiply by servings
            if (!is_null($row->recipe_id) && (int)$row->recipe_id > 0) {
                $items = DB::table('recipe_items')
                    ->where('recipe_id', $row->recipe_id)
                    ->get();

                if ($items->count()) {
                    $perServing = [
                        'calories' => 0, 'protein' => 0, 'carbs' => 0, 'fat' => 0,
                        'fiber' => 0, 'sugar' => 0, 'sodium' => 0, 'carbon_footprint' => 0,
                    ];

                    foreach ($items as $it) {
                        // tolerate either column name: quantity_g (new) or grams (old)
                        $qtyG = (float)($it->quantity_g ?? $it->grams ?? 0);
                        if ($qtyG <= 0) continue;

                        $food = DB::table('foods')->where('id', $it->food_id)->first();
                        if (!$food) continue;

                        $factor = $qtyG / 100.0;
                        foreach ($map as $foodCol => $key) {
                            $perServing[$key] += $factor * (float)($food->{$foodCol} ?? 0);
                        }
                    }

                    $servings = max(0, (float)($row->servings ?? 0));
                    foreach ($perServing as $key => $val) {
                        $totals[$key] += $servings * $val;
                    }
                }
            }
        }

        return $totals;
    }
}
