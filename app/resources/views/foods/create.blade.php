@extends('layouts.app')

@section('content')
<h1>Create Food</h1>

@if ($errors->any())
    <div style="color:#b00020;margin-bottom:12px;">
        <ul style="margin:0;padding-left:18px;">
            @foreach ($errors->all() as $e)
                <li>{{ $e }}</li>
            @endforeach
        </ul>
    </div>
@endif

<form method="post" action="{{ route('foods.store') }}" style="max-width:520px;margin:0 auto;text-align:left;">
    @csrf
    <label>Name<br>
        <input type="text" name="name" value="{{ old('name') }}" required>
    </label><br><br>

    <label>Calories (kcal/100g)<br>
        <input type="number" step="any" name="calories" value="{{ old('calories', 0) }}">
    </label><br><br>

    <label>Protein (g/100g)<br>
        <input type="number" step="any" name="protein" value="{{ old('protein', 0) }}">
    </label><br><br>

    <label>Carbs (g/100g)<br>
        <input type="number" step="any" name="carbs" value="{{ old('carbs', 0) }}">
    </label><br><br>

    <label>Fat (g/100g)<br>
        <input type="number" step="any" name="fat" value="{{ old('fat', 0) }}">
    </label><br><br>

    <label>Fiber (g/100g)<br>
        <input type="number" step="any" name="fiber" value="{{ old('fiber', 0) }}">
    </label><br><br>

    <label>Sugar (g/100g)<br>
        <input type="number" step="any" name="sugar" value="{{ old('sugar', 0) }}">
    </label><br><br>

    <label>Sodium (mg/100g)<br>
        <input type="number" step="any" name="sodium_mg" value="{{ old('sodium_mg', 0) }}">
    </label><br><br>

    <label>Carbon footprint (gCO2e/100g)<br>
        <input type="number" step="any" name="carbon_footprint_gco2e" value="{{ old('carbon_footprint_gco2e', 0) }}">
    </label><br><br>

    <button type="submit">Save</button>
    <a href="{{ route('foods.index') }}">Cancel</a>
</form>
@endsection
