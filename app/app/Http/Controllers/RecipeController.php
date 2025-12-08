<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Recipe;
use App\Models\Food;
use App\Models\RecipeItem;

/*
manages CRUD operations for recipes and their ingredient items,
handles recipe creation, display with nutritional calculations,
editing, deletion, and ingredient management
*/
class RecipeController extends Controller
{
    /*
    retrieves and displays all recipes with optional search filtering,
    results are ordered by creation date in descending order
    */
    public function index(Request $request)
    {
        $q = $request->input('q', '');
        
        $recipes = Recipe::when($q, function($query) use ($q) {
                return $query->where('name', 'like', '%'.$q.'%');
            })
            ->orderBy('created_at', 'desc')
            ->get(['id','name','instructions','created_at']);

        return view('recipes.index', compact('recipes', 'q'));
    }

    //displays the form for creating a new recipe
    public function create()
    {
        return view('recipes.create');
    }

    /*
    validates and stores a new recipe in the database,
    associates the recipe with the currently authenticated user
    */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required','string','max:255'],
            'instructions' => ['nullable','string'],
            'servings'     => ['required','integer','min:1','max:100'],
        ]);

        $data['user_id'] = session('user_id');

        $recipe = Recipe::create([
            'user_id'      => $data['user_id'],
            'name'         => $data['name'],
            'instructions' => $data['instructions'] ?? null,
            'servings'     => $data['servings'],
        ]);

        return redirect()->route('recipes.show', $recipe)->with('ok', 'Recipe created. Now add ingredients.');
    }

    /*
    displays a recipe with its ingredients and calculated nutritional values,
    computes per-item nutrition scaled by grams and aggregates totals,
    also calculates per-serving nutrition based on recipe serving count
    */
    public function show(\App\Models\Recipe $recipe)
    {
        //retrieves all foods for the ingredient dropdown
        $foods = \DB::table('foods')
            ->orderBy('name')
            ->get();

        //joins recipe items with foods to calculate nutritional values
        $rawItems = \DB::table('recipe_items as ri')
            ->join('foods as f', 'ri.food_id', '=', 'f.id')
            ->where('ri.recipe_id', $recipe->id)
            ->orderBy('f.name')
            ->select([
                'ri.id',
                'ri.grams',
                'f.name',
                'f.calories',
                'f.protein',
                'f.carbs',
                'f.fat',
                'f.fiber',
                'f.sugar',
                'f.sodium_mg',
                'f.carbon_footprint_gco2e',
            ])
            ->get();

        //initializes totals array for aggregation
        $items = [];
        $totals = [
            'grams' => 0,
            'calories' => 0.0,
            'protein' => 0.0,
            'carbs' => 0.0,
            'fat' => 0.0,
            'fiber' => 0.0,
            'sugar' => 0.0,
            'sodium_mg' => 0.0,
            'carbon_footprint_gco2e' => 0.0,
        ];

        //computes per-item values scaled by grams and accumulates totals
        foreach ($rawItems as $r) {
            $factor = max(0, (float)$r->grams) / 100.0;

            $item = (object)[
                'id'    => $r->id,
                'name'  => $r->name,
                'grams' => (int)$r->grams,
                'calories' => round($factor * (float)$r->calories, 2),
                'protein'  => round($factor * (float)$r->protein, 2),
                'carbs'    => round($factor * (float)$r->carbs, 2),
                'fat'      => round($factor * (float)$r->fat, 2),
                'fiber'    => round($factor * (float)$r->fiber, 2),
                'sugar'    => round($factor * (float)$r->sugar, 2),
                'sodium_mg' => round($factor * (float)$r->sodium_mg, 2),
                'carbon_footprint_gco2e' => round($factor * (float)$r->carbon_footprint_gco2e, 2),
            ];
            $items[] = $item;

            $totals['grams'] += $item->grams;
            $totals['calories'] += $item->calories;
            $totals['protein']  += $item->protein;
            $totals['carbs']    += $item->carbs;
            $totals['fat']      += $item->fat;
            $totals['fiber']    += $item->fiber;
            $totals['sugar']    += $item->sugar;
            $totals['sodium_mg'] += $item->sodium_mg;
            $totals['carbon_footprint_gco2e'] += $item->carbon_footprint_gco2e;
        }

        //rounds totals for display
        foreach ($totals as $k => $v) {
            $totals[$k] = $k === 'grams' ? (int)$v : round($v, 2);
        }

        //calculates per-serving nutrition
        $servings = max(1, (int)($recipe->servings ?? 1));
        $perServing = [];
        foreach ($totals as $k => $v) {
            $perServing[$k] = $k === 'grams' ? (int)round($v / $servings) : round($v / $servings, 2);
        }

        return view('recipes.show', [
            'recipe' => $recipe,
            'foods'  => $foods,
            'items'  => $items,
            'totals' => $totals,
            'perServing' => $perServing,
        ]);
    }

    //displays the form for editing an existing recipe
    public function edit(Recipe $recipe)
    {
        return view('recipes.edit', compact('recipe'));
    }

    //validates and updates an existing recipe in the database
    public function update(Request $request, Recipe $recipe)
    {
        $data = $request->validate([
            'name'         => ['required','string','max:255'],
            'instructions' => ['nullable','string'],
            'servings'     => ['required','integer','min:1','max:100'],
        ]);

        $recipe->update([
            'name'         => $data['name'],
            'instructions' => $data['instructions'] ?? null,
            'servings'     => $data['servings'],
        ]);

        return redirect()->route('recipes.show', $recipe)->with('ok', 'Recipe updated.');
    }

    /*
    deletes a recipe and its associated ingredient items,
    cascading delete removes all recipe_items automatically
    */
    public function destroy(Recipe $recipe)
    {
        //check if recipe is used in any intake records
        $usedInIntakes = \DB::table('intakes')
            ->where('recipe_id', $recipe->id)
            ->exists();

        if ($usedInIntakes) {
            return redirect()->route('recipes.index')
                ->with('error', 'Cannot delete this recipe because it is used in intake records. Remove the intake entries first.');
        }

        //check if recipe is used in any meal plans
        $usedInMealPlans = \DB::table('meal_plans')
            ->where('recipe_id', $recipe->id)
            ->exists();

        if ($usedInMealPlans) {
            return redirect()->route('recipes.index')
                ->with('error', 'Cannot delete this recipe because it is used in meal plans. Remove it from meal plans first.');
        }

        $recipe->delete();
        return redirect()->route('recipes.index')->with('ok', 'Recipe deleted.');
    }

    //adds a food ingredient to a recipe with specified gram quantity
    public function addItem(\Illuminate\Http\Request $request, \App\Models\Recipe $recipe)
    {
        $validated = $request->validate([
            'food_id' => ['required', 'exists:foods,id'],
            'grams'   => ['nullable', 'numeric', 'min:0', 'max:200000'],
        ]);

        $grams = (int)($validated['grams'] ?? 0);

        \DB::table('recipe_items')->insert([
            'recipe_id' => $recipe->id,
            'food_id'   => (int)$validated['food_id'],
            'grams'     => $grams,
            'created_at'=> now(),
            'updated_at'=> now(),
        ]);

        return redirect()
            ->route('recipes.show', $recipe)
            ->with('ok', 'Ingredient added.');
    }

    //removes an ingredient from a recipe after verifying association
    public function removeItem(Recipe $recipe, RecipeItem $item)
    {
        if ($item->recipe_id === $recipe->id) {
            $item->delete();
        }
        return back()->with('ok', 'Food removed.');
    }
}