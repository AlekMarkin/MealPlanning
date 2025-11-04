<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Intake;
use App\Models\Food;
use App\Models\Recipe;
use App\Models\RecipeItem;

class IntakeController extends Controller
{
    // list intakes for a given day + totals
    public function index(Request $request)
    {
        // FIX: make sure userId is defined
        $userId = auth()->id() ?? session('user_id');
        if (!$userId) {
            return redirect('/')->with('error', 'Please sign in first.');
        }

        $date = $request->query('date') ?: now()->toDateString();

        // preload lists for the add-forms
        $foods   = Food::where('user_id', $userId)->orderBy('name')->get();
        $recipes = Recipe::where('user_id', $userId)->orderBy('name')->get();

        // intakes of the day
        $intakes = Intake::where('user_id', $userId)
            ->whereDate('intake_date', $date)
            ->orderBy('created_at')
            ->get();

        // compute totals (from foods per 100g and recipe items)
        $totals = [
            'calories' => 0, 'protein' => 0, 'carbs' => 0, 'fat' => 0,
            'fiber' => 0, 'sugar' => 0, 'sodium_mg' => 0, 'carbon_footprint_gco2e' => 0,
        ];

        foreach ($intakes as $in) {
            if ($in->food_id) {
                $f = $foods->firstWhere('id', $in->food_id) ?: Food::find($in->food_id);
                if ($f) {
                    $factor = ($in->quantity_g ?: 0) / 100;
                    $totals['calories'] += $f->calories * $factor;
                    $totals['protein']  += $f->protein * $factor;
                    $totals['carbs']    += $f->carbs * $factor;
                    $totals['fat']      += $f->fat * $factor;
                    $totals['fiber']    += $f->fiber * $factor;
                    $totals['sugar']    += $f->sugar * $factor;
                    $totals['sodium_mg'] += $f->sodium_mg * $factor;
                    $totals['carbon_footprint_gco2e'] += $f->carbon_footprint_gco2e * $factor;
                }
            } elseif ($in->recipe_id) {
                $recipe = $recipes->firstWhere('id', $in->recipe_id) ?: Recipe::find($in->recipe_id);
                if ($recipe) {
                    $items = RecipeItem::with('food')->where('recipe_id', $recipe->id)->get();
                    // one "serving" is current sum of items (simple model)
                    $servings = max(1.0, (float)($in->servings ?? 1));
                    foreach ($items as $it) {
                        $f = $it->food;
                        if ($f) {
                            $factor = ($it->quantity_g ?: 0) / 100 * $servings;
                            $totals['calories'] += $f->calories * $factor;
                            $totals['protein']  += $f->protein * $factor;
                            $totals['carbs']    += $f->carbs * $factor;
                            $totals['fat']      += $f->fat * $factor;
                            $totals['fiber']    += $f->fiber * $factor;
                            $totals['sugar']    += $f->sugar * $factor;
                            $totals['sodium_mg'] += $f->sodium_mg * $factor;
                            $totals['carbon_footprint_gco2e'] += $f->carbon_footprint_gco2e * $factor;
                        }
                    }
                }
            }
        }

        return view('intakes.index', [
            'date'    => $date,
            'foods'   => $foods,
            'recipes' => $recipes,
            'intakes' => $intakes,
            'totals'  => $totals,
        ]);
    }

    // add a single food (grams based)
    public function storeFood(Request $request)
    {
        $userId = auth()->id() ?? session('user_id');
        if (!$userId) return redirect('/')->with('error', 'Please sign in first.');

        $data = $request->validate([
            'food_id' => ['required','integer','exists:foods,id'],
            'grams'   => ['nullable','numeric','min:0'],
            'date'    => ['nullable','date'],
        ]);

        $whenDate   = $data['date'] ?? now()->toDateString();
        $consumedAt = $whenDate . ' 12:00:00';

        Intake::create([
            'user_id'     => $userId,
            'intake_date' => $whenDate,
            'consumed_at' => $consumedAt,            // ensure it’s set
            'food_id'     => (int) $data['food_id'],
            'recipe_id'   => null,
            'quantity_g'  => (int) ($data['grams'] ?? 100), // default 100g
            'servings'    => 0,
        ]);

        return back()->with('ok', 'Food added to daily intake.');
    }

    // add a recipe (servings based)
    public function storeRecipe(Request $request)
    {
        $userId = auth()->id() ?? session('user_id');
        if (!$userId) return redirect('/')->with('error', 'Please sign in first.');

        $data = $request->validate([
            'recipe_id' => ['required','integer','exists:recipes,id'],
            'servings'  => ['nullable','numeric','min:0'],
            'date'      => ['nullable','date'],
        ]);

        $whenDate   = $data['date'] ?? now()->toDateString();
        $consumedAt = $whenDate . ' 12:00:00';

        Intake::create([
            'user_id'     => $userId,
            'intake_date' => $whenDate,
            'consumed_at' => $consumedAt,           // ensure it’s set
            'food_id'     => null,
            'recipe_id'   => (int) $data['recipe_id'],
            'quantity_g'  => 0,
            'servings'    => (float) ($data['servings'] ?? 1),
        ]);

        return back()->with('ok', 'Recipe added to daily intake.');
    }

    // optional: delete an intake row
    public function destroy(Intake $intake)
    {
        $userId = auth()->id() ?? session('user_id');
        if ($intake->user_id !== $userId) {
            return back()->with('error', 'Not allowed.');
        }
        $intake->delete();
        return back()->with('ok', 'Removed.');
    }
}
