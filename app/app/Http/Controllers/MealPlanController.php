<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class MealPlanController extends Controller
{
    //display all meal plans
    public function index()
    {
        if (!session('user_id')) {
            return redirect()->route('home')->with('error', 'Please log in first');
        }

        $userId = session('user_id');

        //get meal plans with optional recipe details
        $mealPlans = DB::table('meal_plans')
            ->leftJoin('recipes', 'meal_plans.recipe_id', '=', 'recipes.id')
            ->where('meal_plans.user_id', $userId)
            ->select(
                'meal_plans.*',
                'recipes.name as recipe_name',
                'recipes.description as recipe_description'
            )
            ->orderBy('meal_plans.meal_date', 'desc')
            ->orderBy('meal_plans.meal_type', 'asc')
            ->get();

        //group by date for better display
        $groupedPlans = $mealPlans->groupBy('meal_date');

        return view('meal-plans.index', compact('groupedPlans'));
    }

    //show form to create new meal plan
    public function create()
    {
        if (!session('user_id')) {
            return redirect()->route('home')->with('error', 'Please log in first');
        }

        $userId = session('user_id');

        //get all recipes for the dropdown
        $recipes = DB::table('recipes')->orderBy('name', 'asc')->get();

        //get user goals
        $goals = DB::table('goals')
            ->where('user_id', $userId)
            ->get();

        //get suggested recipes based on goals (smart filtering)
        $suggestions = [];
        if ($goals->count() > 0) {
            $query = DB::table('recipes as r')
                ->leftJoin('recipe_items as ri', 'r.id', '=', 'ri.recipe_id')
                ->leftJoin('foods as f', 'ri.food_id', '=', 'f.id')
                ->where('r.user_id', $userId)
                ->select(
                    'r.id',
                    'r.name',
                    DB::raw('AVG(f.calories) as avg_calories'),
                    DB::raw('AVG(f.protein) as avg_protein'),
                    DB::raw('AVG(f.carbs) as avg_carbs'),
                    DB::raw('AVG(f.fat) as avg_fat'),
                    DB::raw('AVG(f.fiber) as avg_fiber'),
                    DB::raw('AVG(f.carbon_footprint_gco2e) as avg_carbon')
                )
                ->groupBy('r.id', 'r.name');

            //filter based on user goals using HAVING clause (more lenient - 50% margin)
            foreach ($goals as $goal) {
                if ($goal->metric === 'calories') {
                    //allow up to 150% of target
                    $query->having(DB::raw('AVG(f.calories)'), '<=', $goal->target_value * 1.5);
                } elseif ($goal->metric === 'protein') {
                    //allow down to 50% of target
                    $query->having(DB::raw('AVG(f.protein)'), '>=', $goal->target_value * 0.5);
                } elseif ($goal->metric === 'carbs') {
                    $query->having(DB::raw('AVG(f.carbs)'), '<=', $goal->target_value * 1.5);
                } elseif ($goal->metric === 'fat') {
                    $query->having(DB::raw('AVG(f.fat)'), '<=', $goal->target_value * 1.5);
                } elseif ($goal->metric === 'fiber') {
                    $query->having(DB::raw('AVG(f.fiber)'), '>=', $goal->target_value * 0.5);
                } elseif ($goal->metric === 'sugar') {
                    $query->having(DB::raw('AVG(f.sugar)'), '<=', $goal->target_value * 1.5);
                } elseif ($goal->metric === 'sodium') {
                    $query->having(DB::raw('AVG(f.sodium_mg)'), '<=', $goal->target_value * 1.5);
                } elseif ($goal->metric === 'carbon_footprint_gco2e') {
                    $query->having(DB::raw('AVG(f.carbon_footprint_gco2e)'), '<=', $goal->target_value * 1.5);
                }
            }

            $suggestedRecipes = $query->limit(5)->get();
            
            //if no suggestions with strict filter, show any 5 recipes
            if ($suggestedRecipes->count() == 0) {
                $suggestedRecipes = DB::table('recipes as r')
                    ->leftJoin('recipe_items as ri', 'r.id', '=', 'ri.recipe_id')
                    ->leftJoin('foods as f', 'ri.food_id', '=', 'f.id')
                    ->where('r.user_id', $userId)
                    ->select(
                        'r.id',
                        'r.name',
                        DB::raw('AVG(f.calories) as avg_calories'),
                        DB::raw('AVG(f.protein) as avg_protein'),
                        DB::raw('AVG(f.carbs) as avg_carbs'),
                        DB::raw('AVG(f.fat) as avg_fat'),
                        DB::raw('AVG(f.fiber) as avg_fiber'),
                        DB::raw('AVG(f.carbon_footprint_gco2e) as avg_carbon')
                    )
                    ->groupBy('r.id', 'r.name')
                    ->limit(5)
                    ->get();
            }
            
            $suggestions = $suggestedRecipes;
        }

        return view('meal-plans.create', compact('recipes', 'goals', 'suggestions'));
    }

    //store new meal plan
    public function store(Request $request)
    {
        if (!session('user_id')) {
            return redirect()->route('home')->with('error', 'Please log in first');
        }

        //custom validation: require either recipe_id OR custom_food_name
        $request->validate([
            'meal_date' => ['required', 'date'],
            'meal_type' => ['required', 'in:breakfast,lunch,dinner,snack'],
        ]);

        //check that either recipe or custom food is provided
        if (!$request->filled('recipe_id') && !$request->filled('custom_food_name')) {
            return back()
                ->with('error', 'Please select a recipe or enter a custom food name')
                ->withInput();
        }

        //if recipe is selected, validate it
        if ($request->filled('recipe_id')) {
            $request->validate([
                'recipe_id' => ['integer', 'exists:recipes,id'],
            ]);
        }

        //if custom food is entered, validate it
        if ($request->filled('custom_food_name')) {
            $request->validate([
                'custom_food_name' => ['string', 'max:255'],
            ]);
        }

        $userId = session('user_id');

        //check if meal plan already exists for this date and meal type
        $exists = DB::table('meal_plans')
            ->where('user_id', $userId)
            ->where('meal_date', $request->meal_date)
            ->where('meal_type', $request->meal_type)
            ->exists();

        if ($exists) {
            return back()
                ->with('error', 'You already have a meal plan for this date and meal type')
                ->withInput();
        }

        DB::table('meal_plans')->insert([
            'user_id' => $userId,
            'recipe_id' => $request->filled('recipe_id') ? $request->recipe_id : null,
            'custom_food_name' => $request->filled('custom_food_name') ? $request->custom_food_name : null,
            'meal_date' => $request->meal_date,
            'meal_type' => $request->meal_type,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('meal-plans.index')
            ->with('success', 'Meal plan added successfully!');
    }

    //show form to edit meal plan
    public function edit($id)
    {
        if (!session('user_id')) {
            return redirect()->route('home')->with('error', 'Please log in first');
        }

        $userId = session('user_id');

        $mealPlan = DB::table('meal_plans')
            ->where('id', $id)
            ->where('user_id', $userId)
            ->first();

        if (!$mealPlan) {
            return redirect()->route('meal-plans.index')
                ->with('error', 'Meal plan not found');
        }

        //get all recipes for the dropdown
        $recipes = DB::table('recipes')->orderBy('name', 'asc')->get();

        //get user goals
        $goals = DB::table('goals')
            ->where('user_id', $userId)
            ->get();

        //get suggested recipes based on goals (smart filtering)
        $suggestions = [];
        if ($goals->count() > 0) {
            $query = DB::table('recipes as r')
                ->leftJoin('recipe_items as ri', 'r.id', '=', 'ri.recipe_id')
                ->leftJoin('foods as f', 'ri.food_id', '=', 'f.id')
                ->where('r.user_id', $userId)
                ->select(
                    'r.id',
                    'r.name',
                    DB::raw('AVG(f.calories) as avg_calories'),
                    DB::raw('AVG(f.protein) as avg_protein'),
                    DB::raw('AVG(f.carbs) as avg_carbs'),
                    DB::raw('AVG(f.fat) as avg_fat'),
                    DB::raw('AVG(f.fiber) as avg_fiber'),
                    DB::raw('AVG(f.carbon_footprint_gco2e) as avg_carbon')
                )
                ->groupBy('r.id', 'r.name');

            //filter based on user goals using HAVING clause (more lenient - 50% margin)
            foreach ($goals as $goal) {
                if ($goal->metric === 'calories') {
                    //allow up to 150% of target
                    $query->having(DB::raw('AVG(f.calories)'), '<=', $goal->target_value * 1.5);
                } elseif ($goal->metric === 'protein') {
                    //allow down to 50% of target
                    $query->having(DB::raw('AVG(f.protein)'), '>=', $goal->target_value * 0.5);
                } elseif ($goal->metric === 'carbs') {
                    $query->having(DB::raw('AVG(f.carbs)'), '<=', $goal->target_value * 1.5);
                } elseif ($goal->metric === 'fat') {
                    $query->having(DB::raw('AVG(f.fat)'), '<=', $goal->target_value * 1.5);
                } elseif ($goal->metric === 'fiber') {
                    $query->having(DB::raw('AVG(f.fiber)'), '>=', $goal->target_value * 0.5);
                } elseif ($goal->metric === 'sugar') {
                    $query->having(DB::raw('AVG(f.sugar)'), '<=', $goal->target_value * 1.5);
                } elseif ($goal->metric === 'sodium') {
                    $query->having(DB::raw('AVG(f.sodium_mg)'), '<=', $goal->target_value * 1.5);
                } elseif ($goal->metric === 'carbon_footprint_gco2e') {
                    $query->having(DB::raw('AVG(f.carbon_footprint_gco2e)'), '<=', $goal->target_value * 1.5);
                }
            }

            $suggestedRecipes = $query->limit(5)->get();
            
            //if no suggestions with strict filter, show any 5 recipes
            if ($suggestedRecipes->count() == 0) {
                $suggestedRecipes = DB::table('recipes as r')
                    ->leftJoin('recipe_items as ri', 'r.id', '=', 'ri.recipe_id')
                    ->leftJoin('foods as f', 'ri.food_id', '=', 'f.id')
                    ->where('r.user_id', $userId)
                    ->select(
                        'r.id',
                        'r.name',
                        DB::raw('AVG(f.calories) as avg_calories'),
                        DB::raw('AVG(f.protein) as avg_protein'),
                        DB::raw('AVG(f.carbs) as avg_carbs'),
                        DB::raw('AVG(f.fat) as avg_fat'),
                        DB::raw('AVG(f.fiber) as avg_fiber'),
                        DB::raw('AVG(f.carbon_footprint_gco2e) as avg_carbon')
                    )
                    ->groupBy('r.id', 'r.name')
                    ->limit(5)
                    ->get();
            }
            
            $suggestions = $suggestedRecipes;
        }

        return view('meal-plans.edit', compact('mealPlan', 'recipes', 'goals', 'suggestions'));
    }

    //update existing meal plan
    public function update(Request $request, $id)
    {
        if (!session('user_id')) {
            return redirect()->route('home')->with('error', 'Please log in first');
        }

        //custom validation
        $request->validate([
            'meal_date' => ['required', 'date'],
            'meal_type' => ['required', 'in:breakfast,lunch,dinner,snack'],
        ]);

        //check that either recipe or custom food is provided
        if (!$request->filled('recipe_id') && !$request->filled('custom_food_name')) {
            return back()
                ->with('error', 'Please select a recipe or enter a custom food name')
                ->withInput();
        }

        //if recipe is selected, validate it
        if ($request->filled('recipe_id')) {
            $request->validate([
                'recipe_id' => ['integer', 'exists:recipes,id'],
            ]);
        }

        //if custom food is entered, validate it
        if ($request->filled('custom_food_name')) {
            $request->validate([
                'custom_food_name' => ['string', 'max:255'],
            ]);
        }

        $mealPlan = DB::table('meal_plans')
            ->where('id', $id)
            ->where('user_id', session('user_id'))
            ->first();

        if (!$mealPlan) {
            return redirect()->route('meal-plans.index')
                ->with('error', 'Meal plan not found');
        }

        //check if another meal plan exists for this date and meal type (excluding current one)
        $exists = DB::table('meal_plans')
            ->where('user_id', session('user_id'))
            ->where('meal_date', $request->meal_date)
            ->where('meal_type', $request->meal_type)
            ->where('id', '!=', $id)
            ->exists();

        if ($exists) {
            return back()
                ->with('error', 'You already have a meal plan for this date and meal type')
                ->withInput();
        }

        DB::table('meal_plans')
            ->where('id', $id)
            ->update([
                'recipe_id' => $request->filled('recipe_id') ? $request->recipe_id : null,
                'custom_food_name' => $request->filled('custom_food_name') ? $request->custom_food_name : null,
                'meal_date' => $request->meal_date,
                'meal_type' => $request->meal_type,
                'updated_at' => now(),
            ]);

        return redirect()->route('meal-plans.index')
            ->with('success', 'Meal plan updated successfully!');
    }

    //delete meal plan
    public function destroy($id)
    {
        if (!session('user_id')) {
            return redirect()->route('home')->with('error', 'Please log in first');
        }

        $deleted = DB::table('meal_plans')
            ->where('id', $id)
            ->where('user_id', session('user_id'))
            ->delete();

        if (!$deleted) {
            return redirect()->route('meal-plans.index')
                ->with('error', 'Meal plan not found');
        }

        return redirect()->route('meal-plans.index')
            ->with('success', 'Meal plan deleted successfully!');
    }
}