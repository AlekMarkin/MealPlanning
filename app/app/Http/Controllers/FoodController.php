<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

/*
manages CRUD operations for food items in the nutrition database,
handles food creation, retrieval, updating, and deletion with
dependency checking for related intake and recipe records
*/
class FoodController extends Controller
{
    /*
    retrieves and displays a paginated list of all foods,
    supports optional search filtering by food name
    */
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $foods = Food::when($q, fn($query) => $query->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('foods.index', compact('foods', 'q'));
    }

    //displays the form for creating a new food entry
    public function create()
    {
        return view('foods.create');
    }

    /*
    validates and stores a new food entry in the database,
    sets default values of zero for any missing nutritional fields
    */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name'                   => ['required', 'string', 'max:120'],
            'calories'               => ['nullable', 'numeric', 'min:0'],
            'protein'                => ['nullable', 'numeric', 'min:0'],
            'carbs'                  => ['nullable', 'numeric', 'min:0'],
            'fat'                    => ['nullable', 'numeric', 'min:0'],
            'fiber'                  => ['nullable', 'numeric', 'min:0'],
            'sugar'                  => ['nullable', 'numeric', 'min:0'],
            'sodium_mg'              => ['nullable', 'numeric', 'min:0'],
            'carbon_footprint_gco2e' => ['nullable', 'numeric', 'min:0'],
        ]);

        //sets default values for missing numeric fields
        foreach (['calories','protein','carbs','fat','fiber','sugar','sodium_mg','carbon_footprint_gco2e'] as $k) {
            $validated[$k] = $validated[$k] ?? 0;
        }

        $validated['user_id'] = session('user_id');

        Food::create($validated);

        return redirect()->route('foods.index')->with('ok', 'Food created');
    }

    //displays the form for editing an existing food entry
    public function edit(Food $food)
    {
        return view('foods.edit', compact('food'));
    }

    /*
    validates and updates an existing food entry in the database,
    sets default values of zero for any missing nutritional fields
    */
    public function update(Request $request, Food $food)
    {
        $validated = $request->validate([
            'name'                   => ['required', 'string', 'max:120'],
            'calories'               => ['nullable', 'numeric', 'min:0'],
            'protein'                => ['nullable', 'numeric', 'min:0'],
            'carbs'                  => ['nullable', 'numeric', 'min:0'],
            'fat'                    => ['nullable', 'numeric', 'min:0'],
            'fiber'                  => ['nullable', 'numeric', 'min:0'],
            'sugar'                  => ['nullable', 'numeric', 'min:0'],
            'sodium_mg'              => ['nullable', 'numeric', 'min:0'],
            'carbon_footprint_gco2e' => ['nullable', 'numeric', 'min:0'],
        ]);

        //sets default values for missing numeric fields
        foreach (['calories','protein','carbs','fat','fiber','sugar','sodium_mg','carbon_footprint_gco2e'] as $k) {
            $validated[$k] = $validated[$k] ?? 0;
        }

        $food->update($validated);

        return redirect()->route('foods.index')->with('ok', 'Food updated');
    }

    /*
    deletes a food entry after verifying no dependencies exist,
    checks for references in intake records and recipe items
    before allowing deletion to maintain referential integrity
    */
    public function destroy(Food $food)
    {
        //checks if food is used in any intake records
        $usedInIntakes = DB::table('intakes')
            ->where('food_id', $food->id)
            ->exists();

        if ($usedInIntakes) {
            return redirect()->route('foods.index')
                ->with('error', 'Cannot delete this food because it is used in intake records. Remove the intake entries first.');
        }

        //checks if food is used in any recipe items
        $usedInRecipes = DB::table('recipe_items')
            ->where('food_id', $food->id)
            ->exists();

        if ($usedInRecipes) {
            return redirect()->route('foods.index')
                ->with('error', 'Cannot delete this food because it is used in recipes. Remove it from recipes first.');
        }

        $food->delete();
        return redirect()->route('foods.index')->with('ok', 'Food deleted');
    }
}