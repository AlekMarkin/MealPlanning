@extends('layouts.app')

@section('content')
<div style="max-width: 980px; margin: 0 auto;">
    <h1 style="margin-bottom: 12px;">Foods</h1>

    @if(session('ok'))
        <div style="background:#e6ffed; padding:10px; border:1px solid #b7eb8f; margin-bottom:12px;">
            {{ session('ok') }}
        </div>
    @endif

    <div style="display:flex; gap:8px; align-items:center; margin-bottom:12px;">
        <form method="get" action="{{ route('foods.index') }}" style="display:flex; gap:8px;">
            <input type="text" name="q" value="{{ old('q', $q ?? request('q')) }}" placeholder="Search name…" />
            <button type="submit">Search</button>
        </form>
        <a href="{{ route('foods.create') }}" style="margin-left:auto;">+ New Food</a>
    </div>

    <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>Name</th>
            <th>kcal/100g</th>
            <th>Protein (g)</th>
            <th>Carbs (g)</th>
            <th>Fat (g)</th>
            <th>Fiber (g)</th>
            <th>Sugar (g)</th>
            <th>Sodium (mg)</th>
            <th>gCO2e/100g</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse($foods as $f)
            <tr>
                <td>{{ $f->name }}</td>
                <td>{{ $f->calories }}</td>
                <td>{{ $f->protein }}</td>
                <td>{{ $f->carbs }}</td>
                <td>{{ $f->fat }}</td>
                <td>{{ $f->fiber }}</td>
                <td>{{ $f->sugar }}</td>
                <td>{{ $f->sodium_mg }}</td>
                <td>{{ $f->carbon_footprint_gco2e }}</td>
                <td style="white-space:nowrap;">
                    <a href="{{ route('foods.edit', $f) }}">Edit</a>
                    <form method="post" action="{{ route('foods.destroy', $f) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this food?')">Del</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="10">No foods yet.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:10px;">
        @if(method_exists($foods, 'links') && $foods->hasPages())
            {{ $foods->appends(request()->query())->links() }}
        @endif
    </div>
</div>
@endsection
