@extends('layouts.app')

@section('content')
<div style="max-width: 980px; margin: 0 auto;">
    <h1 style="margin-bottom: 12px;">Recipes</h1>

    @if(session('ok'))
        <div style="background:#e6ffed; padding:10px; border:1px solid #b7eb8f; margin-bottom:12px;">
            {{ session('ok') }}
        </div>
    @endif

    <div style="display:flex; gap:8px; align-items:center; margin-bottom:12px;">
        <form method="get" action="{{ route('recipes.index') }}" style="display:flex; gap:8px;">
            <input type="text" name="q" value="{{ old('q', $q ?? request('q')) }}" placeholder="Search name..." />
            <button type="submit">Search</button>
        </form>
        <a href="{{ route('recipes.create') }}" style="margin-left:auto;">+ New Recipe</a>
    </div>

    <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>Name</th>
            <th>Instructions</th>
            <th>Created</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse($recipes as $r)
            <tr>
                <td><a href="{{ route('recipes.show', $r) }}">{{ $r->name }}</a></td>
                <td>{{ Str::limit($r->instructions, 50) ?? '–' }}</td>
                <td>{{ $r->created_at ? $r->created_at->format('Y-m-d') : '–' }}</td>
                <td style="white-space:nowrap;">
                    <a href="{{ route('recipes.edit', $r) }}">Edit</a>
                    <form method="post" action="{{ route('recipes.destroy', $r) }}" style="display:inline;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" onclick="return confirm('Delete this recipe?')">Del</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="4">No recipes yet.</td></tr>
        @endforelse
        </tbody>
    </table>

    <div style="margin-top:10px;">
        @if(method_exists($recipes, 'links') && $recipes->hasPages())
            {{ $recipes->appends(request()->query())->render('pagination') }}
        @endif
    </div>
</div>
@endsection