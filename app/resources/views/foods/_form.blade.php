@php
    $food = $food ?? null;
@endphp

@if ($errors->any())
    <div style="background:#fff1f0; border:1px solid #ffa39e; padding:10px; margin-bottom:12px; text-align:left;">
        <b>Fix the following:</b>
        <ul style="margin:6px 0 0 20px;">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="post" action="{{ $action }}" style="display:grid; grid-template-columns: 1fr 1fr; gap:10px; text-align:left;">
    @csrf
    @if(($method ?? 'POST') !== 'POST') @method($method) @endif

    <label style="grid-column: span 2;">
        Name
        <input type="text" name="name" value="{{ old('name', $food->name ?? '') }}" required />
    </label>

    <label>
        Calories /100g
        <input type="number" step="0.01" min="0" name="calories" value="{{ old('calories', $food->calories ?? 0) }}" required />
    </label>
    <label>
        Protein g /100g
        <input type="number" step="0.01" min="0" name="protein" value="{{ old('protein', $food->protein ?? 0) }}" required />
    </label>

    <label>
        Carbs g /100g
        <input type="number" step="0.01" min="0" name="carbs" value="{{ old('carbs', $food->carbs ?? 0) }}" required />
    </label>
    <label>
        Fat g /100g
        <input type="number" step="0.01" min="0" name="fat" value="{{ old('fat', $food->fat ?? 0) }}" required />
    </label>

    <label>
        Fiber g /100g
        <input type="number" step="0.01" min="0" name="fiber" value="{{ old('fiber', $food->fiber ?? 0) }}" required />
    </label>
    <label>
        Sugar g /100g
        <input type="number" step="0.01" min="0" name="sugar" value="{{ old('sugar', $food->sugar ?? 0) }}" required />
    </label>

    <label>
        Sodium mg /100g
        <input type="number" step="0.01" min="0" name="sodium_mg" value="{{ old('sodium_mg', $food->sodium_mg ?? 0) }}" required />
    </label>
    <label>
        gCO2e /100g
        <input type="number" step="0.01" min="0" name="carbon_footprint_gco2e" value="{{ old('carbon_footprint_gco2e', $food->carbon_footprint_gco2e ?? 0) }}" required />
    </label>

    <div style="grid-column: span 2; text-align:center; margin-top:6px;">
        <button type="submit">Save</button>
        <a href="{{ route('foods.index') }}" style="margin-left:8px;">Cancel</a>
    </div>
</form>
