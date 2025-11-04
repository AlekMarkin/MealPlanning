<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\Recipe;
use App\Models\Food;
use App\Models\RecipeItem;

class RecipeController extends Controller
{
    public function index()
    {
        $userId = session('user_id'); // your current session-based auth
        $recipes = Recipe::where('user_id', $userId)
            ->orderBy('created_at', 'desc')
            ->get(['id','name','instructions','created_at']);

        return view('recipes.index', compact('recipes'));
    }

    public function create()
    {
        return view('recipes.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'         => ['required','string','max:255'],
            'instructions' => ['nullable','string'],
        ]);

        $data['user_id'] = session('user_id');

        $recipe = Recipe::create([
            'user_id'      => $data['user_id'],
            'name'         => $data['name'],
            'instructions' => $data['instructions'] ?? null,
        ]);

        return redirect()->route('recipes.show', $recipe)->with('ok', 'Recipe created. Now add ingredients.');
    }

public function show(\App\Models\Recipe $recipe)
{
    // Only the owner can view (adjust if you already gate elsewhere)
    if ((int)$recipe->user_id !== (int)session('user_id')) {
        abort(403);
    }

    // Foods for the “Add ingredient” dropdown (current user’s foods)
    $foods = \DB::table('foods')
        ->where('user_id', session('user_id'))
        ->orderBy('name')
        ->get();

    // Items joined with foods so we have per-100g nutrition
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

    // Compute per-item values (scaled by grams / 100) and totals
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

    // Round totals nicely
    foreach ($totals as $k => $v) {
        $totals[$k] = $k === 'grams' ? (int)$v : round($v, 2);
    }

    return view('recipes.show', [
        'recipe' => $recipe,
        'foods'  => $foods,
        'items'  => $items,
        'totals' => $totals,
    ]);
}


    public function edit(Recipe $recipe)
    {
        $this->authorizeRecipe($recipe);
        return view('recipes.edit', compact('recipe'));
    }

    public function update(Request $request, Recipe $recipe)
    {
        $this->authorizeRecipe($recipe);

        $data = $request->validate([
            'name'         => ['required','string','max:255'],
            'instructions' => ['nullable','string'],
        ]);

        $recipe->update([
            'name'         => $data['name'],
            'instructions' => $data['instructions'] ?? null,
        ]);

        return redirect()->route('recipes.show', $recipe)->with('ok', 'Recipe updated.');
    }

    public function destroy(Recipe $recipe)
    {
        $this->authorizeRecipe($recipe);
        $recipe->delete();
        return redirect()->route('recipes.index')->with('ok', 'Recipe deleted.');
    }

    // Items (keep as you had)
public function addItem(\Illuminate\Http\Request $request, \App\Models\Recipe $recipe)
{
    // Ensure current user owns this recipe (adjust if you already handle elsewhere)
    if ((int)$recipe->user_id !== (int)session('user_id')) {
        abort(403);
    }

    $validated = $request->validate([
        'food_id' => ['required', 'exists:foods,id'],
        'grams'   => ['nullable', 'numeric', 'min:0', 'max:200000'],
    ]);

    $grams = (int)($validated['grams'] ?? 0);

    \DB::table('recipe_items')->insert([
        'recipe_id' => $recipe->id,
        'food_id'   => (int)$validated['food_id'],
        'grams'     => $grams,              // <- always set
        'created_at'=> now(),
        'updated_at'=> now(),
    ]);

    return redirect()
        ->route('recipes.show', $recipe)
        ->with('ok', 'Ingredient added.');
}

    public function removeItem(Recipe $recipe, RecipeItem $item)
    {
        $this->authorizeRecipe($recipe);
        if ($item->recipe_id === $recipe->id) {
            $item->delete();
        }
        return back()->with('ok', 'Food removed.');
    }

    private function authorizeRecipe(Recipe $recipe): void
    {
        if ($recipe->user_id !== session('user_id')) {
            abort(403);
        }
    }
}
