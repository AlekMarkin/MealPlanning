<?php

namespace App\Http\Controllers;

use App\Models\Food;
use Illuminate\Http\Request;

class FoodController extends Controller
{
    public function index(Request $request)
    {
        $q = $request->string('q')->toString();

        $foods = Food::where('user_id', session('user_id'))
            ->when($q, fn($query) => $query->where('name', 'like', "%{$q}%"))
            ->orderBy('name')
            ->paginate(10)
            ->withQueryString();

        return view('foods.index', compact('foods', 'q'));
    }

    public function create()
    {
        return view('foods.create');
    }

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

        // Default missing numeric fields to 0
        foreach (['calories','protein','carbs','fat','fiber','sugar','sodium_mg','carbon_footprint_gco2e'] as $k) {
            $validated[$k] = $validated[$k] ?? 0;
        }

        $validated['user_id'] = session('user_id');

        Food::create($validated);

        return redirect()->route('foods.index')->with('ok', 'Food created');
    }

    public function edit(Food $food)
    {
        $this->authorizeAccess($food);
        return view('foods.edit', compact('food'));
    }

    public function update(Request $request, Food $food)
    {
        $this->authorizeAccess($food);

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

        foreach (['calories','protein','carbs','fat','fiber','sugar','sodium_mg','carbon_footprint_gco2e'] as $k) {
            $validated[$k] = $validated[$k] ?? 0;
        }

        $food->update($validated);

        return redirect()->route('foods.index')->with('ok', 'Food updated');
    }

    public function destroy(Food $food)
    {
        $this->authorizeAccess($food);
        $food->delete();
        return redirect()->route('foods.index')->with('ok', 'Food deleted');
    }

    private function authorizeAccess(Food $food)
    {
        if ((int)$food->user_id !== (int)session('user_id')) {
            abort(403);
        }
    }
}
