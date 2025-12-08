@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    {{-- page header with back button --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">Edit Recipe</h1>
        <a href="{{ route('recipes.show', $recipe) }}" style="background-color: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Back</a>
    </div>

    {{-- validation errors --}}
    @if ($errors->any())
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- recipe edit form --}}
    <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <p style="margin-bottom: 20px; color: #666; font-style: italic;">
            <strong>Hint:</strong> Ingredients are added on the recipe page after you save.
        </p>

        <form method="post" action="{{ route('recipes.update', $recipe) }}">
            @csrf @method('PUT')

            {{-- recipe name input --}}
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Recipe Name</label>
                <input type="text" name="name" value="{{ old('name', $recipe->name) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
            </div>

            {{-- servings input --}}
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Number of Servings</label>
                <input type="number" name="servings" value="{{ old('servings', $recipe->servings ?? 1) }}" min="1" max="100" required style="width: 150px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
                <p style="color: #666; font-size: 12px; margin-top: 5px;">How many portions does this recipe make? (e.g., 8 slices of pie)</p>
            </div>

            {{-- instructions textarea --}}
            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Instructions</label>
                <textarea name="instructions" rows="10" placeholder="How to make it…" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box; font-family: Arial, sans-serif;">{{ old('instructions', $recipe->instructions) }}</textarea>
            </div>

            {{-- form action buttons --}}
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="flex: 1; background-color: #4CAF50; color: white; padding: 12px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; font-weight: bold;">Save Recipe</button>
                <a href="{{ route('recipes.show', $recipe) }}" style="flex: 1; text-align: center; background-color: #6c757d; color: white; padding: 12px; text-decoration: none; border-radius: 5px; font-size: 16px; font-weight: bold;">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection