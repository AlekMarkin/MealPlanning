<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Intake;
use App\Models\Food;
use App\Models\Recipe;
use App\Models\RecipeItem;

/*
manages daily food and recipe intake tracking for users,
handles recording of consumed foods and recipes with nutritional
calculations based on quantity (grams) or servings
*/
class IntakeController extends Controller
{
    /*
    displays the daily intake log with nutritional totals and goal progress,
    retrieves all intake entries for the specified date, calculates
    cumulative nutritional values, and compares against user goals
    */
    public function index(Request $request)
    {
        $userId = auth()->id() ?? session('user_id');
        if (!$userId) {
            return redirect('/')->with('error', 'Please sign in first.');
        }

        $date = $request->query('date') ?: now()->toDateString();

        //retrieves all foods and recipes for dropdown selection
        $foods   = Food::orderBy('name')->get();
        $recipes = Recipe::orderBy('name')->get();

        //fetches user's intake entries for the selected date
        $intakes = Intake::where('user_id', $userId)
            ->whereDate('intake_date', $date)
            ->orderBy('created_at')
            ->get();

        //initializes nutritional totals array
        $totals = [
            'calories' => 0, 
            'protein' => 0, 
            'carbs' => 0, 
            'fat' => 0,
            'fiber' => 0, 
            'sugar' => 0, 
            'sodium_mg' => 0, 
            'carbon_footprint' => 0,
        ];

        //calculates nutritional values based on intake entries
        foreach ($intakes as $in) {
            if ($in->food_id) {
                $f = $foods->firstWhere('id', $in->food_id) ?: Food::find($in->food_id);
                if ($f) {
                    //scales nutritional values per 100g
                    $factor = ($in->quantity_g ?: 0) / 100;
                    $totals['calories'] += $f->calories * $factor;
                    $totals['protein']  += $f->protein * $factor;
                    $totals['carbs']    += $f->carbs * $factor;
                    $totals['fat']      += $f->fat * $factor;
                    $totals['fiber']    += $f->fiber * $factor;
                    $totals['sugar']    += $f->sugar * $factor;
                    $totals['sodium_mg'] += $f->sodium_mg * $factor;
                    $totals['carbon_footprint'] += $f->carbon_footprint_gco2e * $factor;
                }
            } elseif ($in->recipe_id) {
                $recipe = $recipes->firstWhere('id', $in->recipe_id) ?: Recipe::find($in->recipe_id);
                if ($recipe) {
                    $items = RecipeItem::with('food')->where('recipe_id', $recipe->id)->get();
                    //intake.servings = how many servings the user ate
                    //recipe.servings = how many servings the recipe makes in total
                    $intakeServings = max(1.0, (float)($in->servings ?? 1));
                    $recipeServings = max(1, (int)($recipe->servings ?? 1));
                    foreach ($items as $it) {
                        $f = $it->food;
                        if ($f) {
                            //calculates nutrition: (ingredient nutrition) * (servings eaten) / (total servings in recipe)
                            $grams = (float)($it->grams ?? 0);
                            $factor = ($grams / 100) * ($intakeServings / $recipeServings);
                            $totals['calories'] += $f->calories * $factor;
                            $totals['protein']  += $f->protein * $factor;
                            $totals['carbs']    += $f->carbs * $factor;
                            $totals['fat']      += $f->fat * $factor;
                            $totals['fiber']    += $f->fiber * $factor;
                            $totals['sugar']    += $f->sugar * $factor;
                            $totals['sodium_mg'] += $f->sodium_mg * $factor;
                            $totals['carbon_footprint'] += $f->carbon_footprint_gco2e * $factor;
                        }
                    }
                }
            }
        }

        //loads user's daily nutritional goals for progress comparison
        $rawGoals = DB::table('goals')
            ->select('metric', 'target_value')
            ->where('user_id', $userId)
            ->where('period', 'daily')
            ->get();

        //maps goal metrics to their target values
        $goals = [];
        foreach ($rawGoals as $g) {
            $goals[$g->metric] = (float) $g->target_value;
        }

        return view('intakes.index', [
            'date'    => $date,
            'foods'   => $foods,
            'recipes' => $recipes,
            'intakes' => $intakes,
            'totals'  => $totals,
            'goals'   => $goals,
        ]);
    }

    /*
    records a food intake entry with gram-based quantity,
    creates an intake record linking the user, food, date, and consumption time
    */
    public function storeFood(Request $request)
    {
        $userId = auth()->id() ?? session('user_id');
        if (!$userId) return redirect('/')->with('error', 'Please sign in first.');

        $data = $request->validate([
            'food_id' => ['required','integer','exists:foods,id'],
            'grams'   => ['nullable','numeric','min:0'],
            'date'    => ['nullable','date'],
            'time'    => ['nullable','date_format:H:i'],
        ]);

        $whenDate   = $data['date'] ?? now()->toDateString();
        $whenTime   = $data['time'] ?? '12:00';
        $consumedAt = $whenDate . ' ' . $whenTime . ':00';

        Intake::create([
            'user_id'     => $userId,
            'intake_date' => $whenDate,
            'consumed_at' => $consumedAt,
            'food_id'     => (int) $data['food_id'],
            'recipe_id'   => null,
            'quantity_g'  => (int) ($data['grams'] ?? 100),
            'servings'    => 0,
        ]);

        return back()->with('ok', 'Food added to daily intake.');
    }

    /*
    records a recipe intake entry with serving-based quantity,
    creates an intake record linking the user, recipe, date, and consumption time
    */
    public function storeRecipe(Request $request)
    {
        $userId = auth()->id() ?? session('user_id');
        if (!$userId) return redirect('/')->with('error', 'Please sign in first.');

        $data = $request->validate([
            'recipe_id' => ['required','integer','exists:recipes,id'],
            'servings'  => ['nullable','numeric','min:0'],
            'date'      => ['nullable','date'],
            'time'      => ['nullable','date_format:H:i'],
        ]);

        $whenDate   = $data['date'] ?? now()->toDateString();
        $whenTime   = $data['time'] ?? '12:00';
        $consumedAt = $whenDate . ' ' . $whenTime . ':00';

        Intake::create([
            'user_id'     => $userId,
            'intake_date' => $whenDate,
            'consumed_at' => $consumedAt,
            'food_id'     => null,
            'recipe_id'   => (int) $data['recipe_id'],
            'quantity_g'  => 0,
            'servings'    => (float) ($data['servings'] ?? 1),
        ]);

        return back()->with('ok', 'Recipe added to daily intake.');
    }

    /*
    deletes an intake entry after verifying user ownership,
    ensures users can only delete their own intake records
    */
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