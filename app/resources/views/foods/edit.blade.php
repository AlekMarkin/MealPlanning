@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">Edit Food</h1>
        <a href="{{ route('foods.index') }}" style="background-color: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Back to Foods</a>
    </div>

    @if ($errors->any())
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <form method="post" action="{{ route('foods.update', $food) }}">
            @csrf
            @method('PUT')

            <!-- Name -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Food Name</label>
                <input type="text" name="name" value="{{ old('name', $food->name) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
            </div>

            <!-- Calories -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Calories (kcal/100g)</label>
                <input type="number" step="any" name="calories" value="{{ old('calories', $food->calories ?? 0) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
            </div>

            <!-- Protein -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Protein (g/100g)</label>
                <input type="number" step="any" name="protein" value="{{ old('protein', $food->protein ?? 0) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
            </div>

            <!-- Carbs -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Carbs (g/100g)</label>
                <input type="number" step="any" name="carbs" value="{{ old('carbs', $food->carbs ?? 0) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
            </div>

            <!-- Fat -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Fat (g/100g)</label>
                <input type="number" step="any" name="fat" value="{{ old('fat', $food->fat ?? 0) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
            </div>

            <!-- Fiber -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Fiber (g/100g)</label>
                <input type="number" step="any" name="fiber" value="{{ old('fiber', $food->fiber ?? 0) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
            </div>

            <!-- Sugar -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Sugar (g/100g)</label>
                <input type="number" step="any" name="sugar" value="{{ old('sugar', $food->sugar ?? 0) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
            </div>

            <!-- Sodium -->
            <div style="margin-bottom: 25px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Sodium (mg/100g)</label>
                <input type="number" step="any" name="sodium_mg" value="{{ old('sodium_mg', $food->sodium_mg ?? 0) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
            </div>

            <!-- Carbon Footprint -->
            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Carbon Footprint (gCO2e/100g)</label>
                <input type="number" step="any" name="carbon_footprint_gco2e" value="{{ old('carbon_footprint_gco2e', $food->carbon_footprint_gco2e ?? 0) }}" style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
            </div>

            <!-- Buttons -->
            <div style="display: flex; gap: 10px;">
                <button type="submit" style="flex: 1; background-color: #4CAF50; color: white; padding: 12px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; font-weight: bold;">Update Food</button>
                <a href="{{ route('foods.index') }}" style="flex: 1; text-align: center; background-color: #6c757d; color: white; padding: 12px; text-decoration: none; border-radius: 5px; font-size: 16px; font-weight: bold;">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection