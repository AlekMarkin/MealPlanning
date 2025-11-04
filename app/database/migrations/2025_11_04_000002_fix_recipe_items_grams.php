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
