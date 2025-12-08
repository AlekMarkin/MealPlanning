<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/*
manages meal planning functionality for authenticated users,
handles scheduling of recipes and foods across different meal types
(breakfast, lunch, dinner, snack) with goal-based recipe suggestions
*/
class MealPlanController extends Controller
{
    /*
    retrieves and displays all meal plans for the authenticated user,
    groups meal plans by date for organized calendar-style presentation
    */
    public function index()
    {
        if (!session('user_id')) {
            return redirect()->route('home')->with('error', 'Please log in first');
        }

        $userId = session('user_id');

        //retrieves meal plans with optional recipe details
        $mealPlans = DB::table('meal_plans')
            ->leftJoin('recipes', 'meal_plans.recipe_id', '=', 'recipes.id')
            ->where('meal_plans.user_id', $userId)
            ->select(
                'meal_plans.*',
                'recipes.name as recipe_name'
            )
            ->orderBy('meal_plans.meal_date', 'desc')
            ->orderBy('meal_plans.meal_type', 'asc')
            ->get();

        //groups meal plans by date for organized display
        $groupedPlans = $mealPlans->groupBy('meal_date');

        return view('meal-plans.index', compact('groupedPlans'));
    }

    /*
    displays the form for creating a new meal plan entry,
    loads available recipes, foods, user goals, and generates suggestions
    */
    public function create()
    {
        if (!session('user_id')) {
            return redirect()->route('home')->with('error', 'Please log in first');
        }

        $userId = session('user_id');

        //retrieves all recipes for dropdown selection
        $recipes = DB::table('recipes')->orderBy('name', 'asc')->get();

        //retrieves all foods for dropdown selection
        $foods = DB::table('foods')->orderBy('name', 'asc')->get();

        //retrieves user's nutritional goals
        $goals = DB::table('goals')
            ->where('user_id', $userId)
            ->get();

        //generates recipe suggestions based on user goals
        $suggestions = $this->getSuggestedRecipes($goals);

        return view('meal-plans.create', compact('recipes', 'foods', 'goals', 'suggestions'));
    }

    /*
    validates and stores a new meal plan entry in the database,
    prevents duplicate entries for the same date and meal type,
    accepts either a recipe, food selection, or custom food name
    */
    public function store(Request $request)
    {
        if (!session('user_id')) {
            return redirect()->route('home')->with('error', 'Please log in first');
        }

        $request->validate([
            'meal_date' => ['required', 'date'],
            'meal_type' => ['required', 'in:breakfast,lunch,dinner,snack'],
        ]);

        //validates that at least one food option is provided
        if (!$request->filled('recipe_id') && !$request->filled('food_id') && !$request->filled('custom_food_name')) {
            return back()
                ->with('error', 'Please select a recipe, food, or enter a custom food name')
                ->withInput();
        }

        if ($request->filled('recipe_id')) {
            $request->validate([
                'recipe_id' => ['integer', 'exists:recipes,id'],
            ]);
        }

        if ($request->filled('custom_food_name')) {
            $request->validate([
                'custom_food_name' => ['string', 'max:255'],
            ]);
        }

        $userId = session('user_id');

        //prevents duplicate meal plans for same date and meal type
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

        //determines custom food name from food selection or direct input
        $customFoodName = null;
        if ($request->filled('food_id')) {
            $food = DB::table('foods')->where('id', $request->food_id)->first();
            if ($food) {
                $customFoodName = $food->name;
            }
        } elseif ($request->filled('custom_food_name')) {
            $customFoodName = $request->custom_food_name;
        }

        DB::table('meal_plans')->insert([
            'user_id' => $userId,
            'recipe_id' => $request->filled('recipe_id') ? $request->recipe_id : null,
            'custom_food_name' => $customFoodName,
            'meal_date' => $request->meal_date,
            'meal_type' => $request->meal_type,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return redirect()->route('meal-plans.index')
            ->with('success', 'Meal plan added successfully!');
    }

    /*
    displays the form for editing an existing meal plan entry,
    verifies ownership before allowing access to the edit form
    */
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

        //retrieves all recipes for dropdown selection
        $recipes = DB::table('recipes')->orderBy('name', 'asc')->get();

        //retrieves all foods for dropdown selection
        $foods = DB::table('foods')->orderBy('name', 'asc')->get();

        //retrieves user's nutritional goals
        $goals = DB::table('goals')
            ->where('user_id', $userId)
            ->get();

        //generates recipe suggestions based on user goals
        $suggestions = $this->getSuggestedRecipes($goals);

        return view('meal-plans.edit', compact('mealPlan', 'recipes', 'foods', 'goals', 'suggestions'));
    }

    /*
    validates and updates an existing meal plan entry,
    prevents duplicate entries for the same date and meal type
    */
    public function update(Request $request, $id)
    {
        if (!session('user_id')) {
            return redirect()->route('home')->with('error', 'Please log in first');
        }

        $request->validate([
            'meal_date' => ['required', 'date'],
            'meal_type' => ['required', 'in:breakfast,lunch,dinner,snack'],
        ]);

        //validates that at least one food option is provided
        if (!$request->filled('recipe_id') && !$request->filled('food_id') && !$request->filled('custom_food_name')) {
            return back()
                ->with('error', 'Please select a recipe, food, or enter a custom food name')
                ->withInput();
        }

        if ($request->filled('recipe_id')) {
            $request->validate([
                'recipe_id' => ['integer', 'exists:recipes,id'],
            ]);
        }

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

        //prevents duplicate meal plans for same date and meal type
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

        //determines custom food name from food selection or direct input
        $customFoodName = null;
        if ($request->filled('food_id')) {
            $food = DB::table('foods')->where('id', $request->food_id)->first();
            if ($food) {
                $customFoodName = $food->name;
            }
        } elseif ($request->filled('custom_food_name')) {
            $customFoodName = $request->custom_food_name;
        }

        DB::table('meal_plans')
            ->where('id', $id)
            ->update([
                'recipe_id' => $request->filled('recipe_id') ? $request->recipe_id : null,
                'custom_food_name' => $customFoodName,
                'meal_date' => $request->meal_date,
                'meal_type' => $request->meal_type,
                'updated_at' => now(),
            ]);

        return redirect()->route('meal-plans.index')
            ->with('success', 'Meal plan updated successfully!');
    }

    //deletes a meal plan entry after verifying ownership
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

    /*
    generates recipe suggestions based on user's nutritional goals,
    filters recipes by average nutritional values with a 50% tolerance margin,
    returns fallback suggestions if no goal-matched recipes are found
    */
    private function getSuggestedRecipes($goals)
    {
        if ($goals->count() == 0) {
            return collect([]);
        }

        $query = DB::table('recipes as r')
            ->leftJoin('recipe_items as ri', 'r.id', '=', 'ri.recipe_id')
            ->leftJoin('foods as f', 'ri.food_id', '=', 'f.id')
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

        //applies goal-based filtering with 50% tolerance margin
        foreach ($goals as $goal) {
            if ($goal->metric === 'calories') {
                $query->having(DB::raw('AVG(f.calories)'), '<=', $goal->target_value * 1.5);
            } elseif ($goal->metric === 'protein') {
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
            } elseif ($goal->metric === 'carbon_footprint') {
                $query->having(DB::raw('AVG(f.carbon_footprint_gco2e)'), '<=', $goal->target_value * 1.5);
            }
        }

        $suggestions = $query->limit(5)->get();

        //returns fallback suggestions if no goal-matched recipes found
        if ($suggestions->count() == 0) {
            $suggestions = DB::table('recipes as r')
                ->leftJoin('recipe_items as ri', 'r.id', '=', 'ri.recipe_id')
                ->leftJoin('foods as f', 'ri.food_id', '=', 'f.id')
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

        return $suggestions;
    }
}