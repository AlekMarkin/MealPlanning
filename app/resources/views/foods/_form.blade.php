@php
    $food = $food ?? null;
@endphp

@if ($errors->any())
    <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
        <b>Fix the following:</b>
        <ul style="margin: 6px 0 0 20px;">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="post" action="{{ $action }}" style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px;">
    @csrf
    @if(($method ?? 'POST') !== 'POST') @method($method) @endif

    <div style="grid-column: span 2;">
        <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Food Name</label>
        <input type="text" name="name" value="{{ old('name', $food->name ?? '') }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
    </div>

    <div>
        <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Calories (kcal/100g)</label>
        <input type="number" step="0.01" min="0" name="calories" value="{{ old('calories', $food->calories ?? 0) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
    </div>
    <div>
        <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Protein (g/100g)</label>
        <input type="number" step="0.01" min="0" name="protein" value="{{ old('protein', $food->protein ?? 0) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
    </div>

    <div>
        <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Carbs (g/100g)</label>
        <input type="number" step="0.01" min="0" name="carbs" value="{{ old('carbs', $food->carbs ?? 0) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
    </div>
    <div>
        <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Fat (g/100g)</label>
        <input type="number" step="0.01" min="0" name="fat" value="{{ old('fat', $food->fat ?? 0) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
    </div>

    <div>
        <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Fiber (g/100g)</label>
        <input type="number" step="0.01" min="0" name="fiber" value="{{ old('fiber', $food->fiber ?? 0) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
    </div>
    <div>
        <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Sugar (g/100g)</label>
        <input type="number" step="0.01" min="0" name="sugar" value="{{ old('sugar', $food->sugar ?? 0) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
    </div>

    <div>
        <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Sodium (mg/100g)</label>
        <input type="number" step="0.01" min="0" name="sodium_mg" value="{{ old('sodium_mg', $food->sodium_mg ?? 0) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
    </div>
    <div>
        <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Carbon Footprint (gCO2e/100g)</label>
        <input type="number" step="0.01" min="0" name="carbon_footprint_gco2e" value="{{ old('carbon_footprint_gco2e', $food->carbon_footprint_gco2e ?? 0) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
    </div>

    <div style="grid-column: span 2; display: flex; gap: 10px; margin-top: 10px;">
        <button type="submit" style="flex: 1; background-color: #4CAF50; color: white; padding: 12px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; font-weight: bold;">Save</button>
        <a href="{{ route('foods.index') }}" style="flex: 1; text-align: center; background-color: #6c757d; color: white; padding: 12px; text-decoration: none; border-radius: 5px; font-size: 16px; font-weight: bold;">Cancel</a>
    </div>
</form>