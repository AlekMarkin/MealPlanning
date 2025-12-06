@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">{{ $recipe->name }}</h1>
        <a href="{{ route('recipes.edit', $recipe) }}" style="background-color: #007bff; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Edit Recipe</a>
    </div>

    @if(session('ok'))
        <div style="background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border: 1px solid #c3e6cb; border-radius: 5px;">
            {{ session('ok') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <!-- Instructions Section -->
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2 style="margin-top: 0; margin-bottom: 15px;">Instructions</h2>
        <div style="white-space: pre-wrap; color: #555; line-height: 1.6;">{{ $recipe->instructions ?? '—' }}</div>
    </div>

    <!-- Add Food Section -->
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <h2 style="margin-top: 0; margin-bottom: 15px;">Add Ingredient</h2>
        <form method="post" action="{{ route('recipes.items.store', $recipe) }}" style="display: flex; gap: 10px; align-items: center; flex-wrap: wrap;">
            @csrf
            <select name="food_id" required style="flex: 1; min-width: 200px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                <option value="">— choose food —</option>
                @foreach($foods as $food)
                    <option value="{{ $food->id }}">{{ $food->name }}</option>
                @endforeach
            </select>

            <input type="number" name="grams" min="0" step="1" value="100" placeholder="grams" style="width: 120px; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;" />
            <button type="submit" style="background-color: #4CAF50; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">Add</button>
        </form>
    </div>

    <!-- Ingredients Table Section -->
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0; margin-bottom: 15px;">Ingredients</h2>

        @if(empty($items))
            <p style="text-align: center; color: #999; padding: 40px 0;">
                No ingredients yet. Add some using the form above.
            </p>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left;">Food</th>
                            <th style="padding: 12px; text-align: left;">Qty (g)</th>
                            <th style="padding: 12px; text-align: left;">kcal</th>
                            <th style="padding: 12px; text-align: left;">Protein</th>
                            <th style="padding: 12px; text-align: left;">Carbs</th>
                            <th style="padding: 12px; text-align: left;">Fat</th>
                            <th style="padding: 12px; text-align: left;">Fiber</th>
                            <th style="padding: 12px; text-align: left;">Sugar</th>
                            <th style="padding: 12px; text-align: left;">Sodium (mg)</th>
                            <th style="padding: 12px; text-align: left;">gCO2e</th>
                            <th style="padding: 12px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($items as $it)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px;">{{ $it->name }}</td>
                                <td style="padding: 12px;">{{ $it->grams }}</td>
                                <td style="padding: 12px;">{{ number_format($it->calories, 2) }}</td>
                                <td style="padding: 12px;">{{ number_format($it->protein, 2) }}</td>
                                <td style="padding: 12px;">{{ number_format($it->carbs, 2) }}</td>
                                <td style="padding: 12px;">{{ number_format($it->fat, 2) }}</td>
                                <td style="padding: 12px;">{{ number_format($it->fiber, 2) }}</td>
                                <td style="padding: 12px;">{{ number_format($it->sugar, 2) }}</td>
                                <td style="padding: 12px;">{{ number_format($it->sodium_mg, 2) }}</td>
                                <td style="padding: 12px;">{{ number_format($it->carbon_footprint_gco2e, 2) }}</td>
                                <td style="padding: 12px; text-align: center;">
                                    <form method="post" action="{{ route('recipes.items.destroy', [$recipe, $it->id]) }}" style="display: inline;" onsubmit="return confirm('Remove this ingredient?');">
                                        @csrf @method('DELETE')
                                        <button type="submit" style="background-color: #dc3545; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>

                    @if(!empty($items))
                    <tfoot>
                        <tr style="font-weight: bold; background-color: #f8f9fa; border-top: 2px solid #dee2e6;">
                            <td style="padding: 12px;">Total</td>
                            <td style="padding: 12px;">{{ $totals['grams'] }}</td>
                            <td style="padding: 12px;">{{ number_format($totals['calories'], 2) }}</td>
                            <td style="padding: 12px;">{{ number_format($totals['protein'], 2) }}</td>
                            <td style="padding: 12px;">{{ number_format($totals['carbs'], 2) }}</td>
                            <td style="padding: 12px;">{{ number_format($totals['fat'], 2) }}</td>
                            <td style="padding: 12px;">{{ number_format($totals['fiber'], 2) }}</td>
                            <td style="padding: 12px;">{{ number_format($totals['sugar'], 2) }}</td>
                            <td style="padding: 12px;">{{ number_format($totals['sodium_mg'], 2) }}</td>
                            <td style="padding: 12px;">{{ number_format($totals['carbon_footprint_gco2e'], 2) }}</td>
                            <td style="padding: 12px;"></td>
                        </tr>
                    </tfoot>
                    @endif
                </table>
            </div>
        @endif
    </div>
</div>
@endsection