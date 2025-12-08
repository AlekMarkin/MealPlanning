<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\Food;
use Illuminate\Http\Request;

/*
manages recipe ingredient items through CRUD operations,
handles adding, updating quantities, and removing food items from recipes
*/
class RecipeItemController extends Controller
{
    /*
    adds a food ingredient to a recipe,
    validates the food exists and creates the association with default or specified grams
    */
    public function store(Request $request, Recipe $recipe)
    {
        $request->validate([
            'food_id' => ['required','integer','exists:foods,id'],
            'grams'   => ['nullable','numeric','min:1'],
        ]);

        $food = Food::where('id', $request->food_id)->firstOrFail();

        RecipeItem::create([
            'recipe_id' => $recipe->id,
            'food_id'   => $food->id,
            'grams'     => $request->input('grams', 100),
        ]);

        return redirect()->route('recipes.show', $recipe)->with('ok','Food added to recipe.');
    }

    /*
    updates the gram quantity of an ingredient in a recipe,
    verifies the item belongs to the specified recipe before updating
    */
    public function update(Request $request, Recipe $recipe, RecipeItem $item)
    {
        abort_unless($item->recipe_id === $recipe->id, 404);

        $request->validate([
            'grams' => ['required','numeric','min:1'],
        ]);

        $item->update(['grams' => $request->grams]);

        return redirect()->route('recipes.show', $recipe)->with('ok','Item updated.');
    }

    /*
    removes an ingredient from a recipe,
    verifies the item belongs to the specified recipe before deletion
    */
    public function destroy(Recipe $recipe, RecipeItem $item)
    {
        abort_unless($item->recipe_id === $recipe->id, 404);

        $item->delete();

        return redirect()->route('recipes.show', $recipe)->with('ok','Item removed.');
    }
}