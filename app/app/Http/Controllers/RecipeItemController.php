<?php

namespace App\Http\Controllers;

use App\Models\Recipe;
use App\Models\RecipeItem;
use App\Models\Food;
use Illuminate\Http\Request;

class RecipeItemController extends Controller
{
    public function store(Request $request, Recipe $recipe)
    {
        abort_unless($recipe->user_id === session('user_id'), 403);

        $request->validate([
            'food_id' => ['required','integer','exists:foods,id'],
            'grams'   => ['nullable','numeric','min:1'],
        ]);

        // Ensure the food belongs to the same user
        $food = Food::where('id', $request->food_id)
                    ->where('user_id', session('user_id'))
                    ->firstOrFail();

        RecipeItem::create([
            'recipe_id' => $recipe->id,
            'food_id'   => $food->id,
            'grams'     => $request->input('grams', 100), // default 100g
        ]);

        return redirect()->route('recipes.show', $recipe)->with('ok','Food added to recipe.');
    }

    public function update(Request $request, Recipe $recipe, RecipeItem $item)
    {
        abort_unless($recipe->user_id === session('user_id'), 403);
        abort_unless($item->recipe_id === $recipe->id, 404);

        $request->validate([
            'grams' => ['required','numeric','min:1'],
        ]);

        $item->update(['grams' => $request->grams]);

        return redirect()->route('recipes.show', $recipe)->with('ok','Item updated.');
    }

    public function destroy(Recipe $recipe, RecipeItem $item)
    {
        abort_unless($recipe->user_id === session('user_id'), 403);
        abort_unless($item->recipe_id === $recipe->id, 404);

        $item->delete();

        return redirect()->route('recipes.show', $recipe)->with('ok','Item removed.');
    }
}
