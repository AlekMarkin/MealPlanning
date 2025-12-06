@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="margin-bottom: 30px;">Edit Meal Plan</h1>

    @if(session('error'))
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <form action="{{ route('meal-plans.update', $mealPlan->id) }}" method="POST" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        @csrf

        <div style="margin-bottom: 20px;">
            <label for="meal_date" style="display: block; margin-bottom: 5px; font-weight: bold;">Date</label>
            <input type="date" 
                   id="meal_date" 
                   name="meal_date" 
                   value="{{ old('meal_date', $mealPlan->meal_date) }}" 
                   required
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        </div>

        <div style="margin-bottom: 20px;">
            <label for="meal_type" style="display: block; margin-bottom: 5px; font-weight: bold;">Meal type</label>
            <select id="meal_type" 
                    name="meal_type" 
                    required
                    style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                <option value="">-- Select meal type --</option>
                <option value="breakfast" {{ old('meal_type', $mealPlan->meal_type) == 'breakfast' ? 'selected' : '' }}>Breakfast</option>
                <option value="lunch" {{ old('meal_type', $mealPlan->meal_type) == 'lunch' ? 'selected' : '' }}>Lunch</option>
                <option value="dinner" {{ old('meal_type', $mealPlan->meal_type) == 'dinner' ? 'selected' : '' }}>Dinner</option>
                <option value="snack" {{ old('meal_type', $mealPlan->meal_type) == 'snack' ? 'selected' : '' }}>Snack</option>
            </select>
        </div>

        <div style="background-color: #f8f9fa; padding: 20px; border-radius: 8px; margin-bottom: 20px;">
            <p style="margin: 0 0 15px 0; font-weight: bold; color: #495057;">Choose one option:</p>
            
            <div style="margin-bottom: 20px;">
                <label for="recipe_id" style="display: block; margin-bottom: 5px; font-weight: bold;">Option 1: select a recipe</label>
                <select id="recipe_id" 
                        name="recipe_id" 
                        onchange="if(this.value) document.getElementById('custom_food_name').value=''"
                        style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                    <option value="">-- Select recipe --</option>
                    @foreach($recipes as $recipe)
                        <option value="{{ $recipe->id }}" {{ old('recipe_id', $mealPlan->recipe_id) == $recipe->id ? 'selected' : '' }}>
                            {{ $recipe->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="text-align: center; color: #6c757d; margin: 15px 0; font-weight: bold;">
                OR
            </div>

            <div>
                <label for="custom_food_name" style="display: block; margin-bottom: 5px; font-weight: bold;">Option 2: enter custom food</label>
                <input type="text" 
                       id="custom_food_name" 
                       name="custom_food_name" 
                       value="{{ old('custom_food_name', $mealPlan->custom_food_name) }}"
                       onchange="if(this.value) document.getElementById('recipe_id').value=''"
                       placeholder="e.g., Oatmeal with fruits, Greek yogurt, Banana"
                       style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
                <small style="color: #666;">Enter any food or meal name</small>
            </div>
        </div>

        @if($suggestions->count() > 0)
        <div style="background-color: #e7f3ff; padding: 20px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #2196F3;">
            <p style="margin: 0 0 15px 0; font-weight: bold; color: #1565c0;">Suggested recipes based on your goals:</p>
            <div style="display: grid; grid-template-columns: 1fr; gap: 10px;">
                @foreach($suggestions as $suggestion)
                <button type="button" 
                        onclick="document.getElementById('recipe_id').value='{{ $suggestion->id }}'; document.getElementById('custom_food_name').value='';" 
                        style="text-align: left; padding: 12px; background: white; border: 1px solid #90caf9; border-radius: 4px; cursor: pointer; transition: all 0.2s;">
                    <strong style="color: #1565c0;">{{ $suggestion->name }}</strong>
                    <div style="font-size: 12px; color: #666; margin-top: 5px;">
                        Protein: {{ round($suggestion->avg_protein, 1) }}g | 
                        Carbs: {{ round($suggestion->avg_carbs, 1) }}g | 
                        Fat: {{ round($suggestion->avg_fat, 1) }}g
                    </div>
                </button>
                @endforeach
            </div>
        </div>
        @elseif($goals->count() == 0)
        <div style="background-color: #fff3cd; padding: 15px; border-radius: 8px; margin-bottom: 20px; border-left: 4px solid #ffc107;">
            <p style="margin: 0; color: #856404;"><strong>Tip:</strong> <a href="{{ route('goals.index') }}" style="color: #856404; text-decoration: underline;">Set your nutrition goals</a> to get personalized recipe suggestions!</p>
        </div>
        @endif

        <div style="display: flex; gap: 10px;">
            <button type="submit" style="flex: 1; background-color: #4CAF50; color: white; padding: 12px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">
                Update Meal Plan
            </button>
            <a href="{{ route('meal-plans.index') }}" style="flex: 1; background-color: #6c757d; color: white; padding: 12px; border: none; border-radius: 5px; font-size: 16px; text-decoration: none; text-align: center; display: block;">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection