@extends('layouts.app')

@section('content')
<h1 style="margin-bottom:8px;">Recipe: {{ $recipe->name }}</h1>

@if(session('ok'))
    <div style="background:#e6ffed;border:1px solid #b7eb8f;padding:8px;margin-bottom:12px;">{{ session('ok') }}</div>
@endif
@if($errors->any())
    <div style="background:#ffecec;border:1px solid #f5aca6;padding:8px;margin-bottom:12px;">
        <ul style="margin:0;padding-left:18px;">
            @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
        </ul>
    </div>
@endif

<div style="max-width:900px;margin:0 auto;text-align:left;">
    <div style="margin-bottom:14px;">
        <div><strong>Instructions:</strong></div>
        <div style="white-space:pre-wrap;">{{ $recipe->instructions ?? '—' }}</div>
        <div style="margin-top:8px;">
            <a href="{{ route('recipes.edit', $recipe) }}">Edit recipe</a>
        </div>
    </div>

    <h3>Add Food</h3>
    <form method="post" action="{{ route('recipes.items.store', $recipe) }}" style="display:flex; gap:8px; align-items:center; margin-bottom:12px;">
        @csrf
        <select name="food_id" required>
            <option value="">— choose food —</option>
            @foreach($foods as $food)
                <option value="{{ $food->id }}">{{ $food->name }}</option>
            @endforeach
        </select>

        <input type="number" name="grams" min="0" step="1" value="100" placeholder="grams" />
        <button type="submit">Add</button>
    </form>

    <h3>Ingredients</h3>
    <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>Food</th>
            <th>Qty (g)</th>
            <th>kcal</th>
            <th>Protein</th>
            <th>Carbs</th>
            <th>Fat</th>
            <th>Fiber</th>
            <th>Sugar</th>
            <th>Sodium (mg)</th>
            <th>gCO2e</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse($items as $it)
            <tr>
                <td>{{ $it->name }}</td>
                <td>{{ $it->grams }}</td>
                <td>{{ number_format($it->calories, 2) }}</td>
                <td>{{ number_format($it->protein, 2) }}</td>
                <td>{{ number_format($it->carbs, 2) }}</td>
                <td>{{ number_format($it->fat, 2) }}</td>
                <td>{{ number_format($it->fiber, 2) }}</td>
                <td>{{ number_format($it->sugar, 2) }}</td>
                <td>{{ number_format($it->sodium_mg, 2) }}</td>
                <td>{{ number_format($it->carbon_footprint_gco2e, 2) }}</td>
                <td style="white-space:nowrap;">
                    <form method="post" action="{{ route('recipes.items.destroy', [$recipe, $it->id]) }}">
                        @csrf @method('DELETE')
                        <button type="submit" onclick="return confirm('Remove this item?')">Del</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="11">No ingredients yet.</td></tr>
        @endforelse
        </tbody>

        @if(!empty($items))
        <tfoot>
            <tr style="font-weight:bold;background:#fafafa;">
                <td>Total</td>
                <td>{{ $totals['grams'] }}</td>
                <td>{{ number_format($totals['calories'], 2) }}</td>
                <td>{{ number_format($totals['protein'], 2) }}</td>
                <td>{{ number_format($totals['carbs'], 2) }}</td>
                <td>{{ number_format($totals['fat'], 2) }}</td>
                <td>{{ number_format($totals['fiber'], 2) }}</td>
                <td>{{ number_format($totals['sugar'], 2) }}</td>
                <td>{{ number_format($totals['sodium_mg'], 2) }}</td>
                <td>{{ number_format($totals['carbon_footprint_gco2e'], 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection
