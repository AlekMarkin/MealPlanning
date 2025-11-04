@extends('layouts.app')

@section('content')
<div style="max-width: 960px; margin: 0 auto; text-align:left;">
    <h1 style="margin-bottom:10px;">Daily Intake</h1>

    @if(session('ok'))
        <div style="background:#e6ffed;border:1px solid #b7eb8f;padding:8px;margin-bottom:12px;">
            {{ session('ok') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#ffecec;border:1px solid #f5aca6;padding:8px;margin-bottom:12px;">
            {{ session('error') }}
        </div>
    @endif
    @if($errors->any())
        <div style="background:#ffecec;border:1px solid #f5aca6;padding:8px;margin-bottom:12px;">
            <ul style="margin:0;padding-left:18px;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Date picker --}}
    <form method="get" action="{{ route('intakes.index') }}" style="display:flex;gap:8px;align-items:center;margin-bottom:14px;">
        <label for="date"><strong>Date:</strong></label>
        <input id="date" type="date" name="date" value="{{ $date }}" />
        <button type="submit">Go</button>
        <a href="{{ route('intakes.index') }}" style="margin-left:auto;">Today</a>
    </form>

    {{-- Add Food --}}
    <fieldset style="border:1px solid #ddd;padding:10px;margin-bottom:12px;">
        <legend><strong>Add Food</strong></legend>
        <form method="post" action="{{ route('intakes.food.store') }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <select name="food_id" required>
                <option value="">— choose food —</option>
                @foreach($foods as $f)
                    <option value="{{ $f->id }}">{{ $f->name }}</option>
                @endforeach
            </select>
            <input type="number" name="grams" min="0" step="1" value="100" placeholder="grams (g)" />
            <button type="submit">Add</button>
            <a href="{{ route('foods.create') }}" style="margin-left:auto;">+ New Food</a>
        </form>
    </fieldset>

    {{-- Add Recipe --}}
    <fieldset style="border:1px solid #ddd;padding:10px;margin-bottom:12px;">
        <legend><strong>Add Recipe</strong></legend>
        <form method="post" action="{{ route('intakes.recipe.store') }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <select name="recipe_id" required>
                <option value="">— choose recipe —</option>
                @foreach($recipes as $r)
                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                @endforeach
            </select>
            <input type="number" name="servings" min="0" step="0.25" value="1" placeholder="servings" />
            <button type="submit">Add</button>
            <a href="{{ route('recipes.create') }}" style="margin-left:auto;">+ New Recipe</a>
        </form>
    </fieldset>

    {{-- Intakes table --}}
    <h3 style="margin:12px 0 6px;">Entries for {{ $date }}</h3>
    <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>Type</th>
            <th>Name</th>
            <th>Qty</th>
            <th>When</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse($intakes as $in)
            <tr>
                <td>{{ $in->food_id ? 'Food' : 'Recipe' }}</td>
                <td>
                    @if($in->food_id)
                        {{ optional(\App\Models\Food::find($in->food_id))->name ?? '—' }}
                    @elseif($in->recipe_id)
                        {{ optional(\App\Models\Recipe::find($in->recipe_id))->name ?? '—' }}
                    @else
                        —
                    @endif
                </td>
                <td>
                    @if($in->food_id)
                        {{ (int)$in->quantity_g }} g
                    @elseif($in->recipe_id)
                        {{ (float)$in->servings }} serving(s)
                    @endif
                </td>
                <td>
                    {{ $in->consumed_at ? \Illuminate\Support\Carbon::parse($in->consumed_at)->format('H:i') : '—' }}
                </td>
                <td>
                    <form method="post" action="{{ route('intakes.destroy', $in) }}" onsubmit="return confirm('Remove this entry?')" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit">Del</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">No entries yet.</td></tr>
        @endforelse
        </tbody>
    </table>

    {{-- Totals --}}
    <h3 style="margin:14px 0 6px;">Totals</h3>
    <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>kcal</th>
            <th>Protein</th>
            <th>Carbs</th>
            <th>Fat</th>
            <th>Fiber</th>
            <th>Sugar</th>
            <th>Sodium (mg)</th>
            <th>gCO2e</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ round($totals['calories'], 2) }}</td>
                <td>{{ round($totals['protein'], 2) }}</td>
                <td>{{ round($totals['carbs'], 2) }}</td>
                <td>{{ round($totals['fat'], 2) }}</td>
                <td>{{ round($totals['fiber'], 2) }}</td>
                <td>{{ round($totals['sugar'], 2) }}</td>
                <td>{{ round($totals['sodium_mg'], 2) }}</td>
                <td>{{ round($totals['carbon_footprint_gco2e'], 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>
@endsection
