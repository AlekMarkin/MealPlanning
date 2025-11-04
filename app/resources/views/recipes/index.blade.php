@extends('layouts.app')

@section('content')
<h1 style="margin-bottom:12px;">Recipes</h1>

@if(session('ok'))
    <div style="background:#e6ffed; padding:10px; border:1px solid #b7eb8f; margin-bottom:12px;">
        {{ session('ok') }}
    </div>
@endif

<div style="display:flex; gap:8px; align-items:center; margin-bottom:12px;">
    <a href="{{ route('recipes.create') }}">+ New Recipe</a>
</div>

<table border="1" cellpadding="6" cellspacing="0" width="100%">
    <thead>
    <tr>
        <th style="width:220px;">Name</th>
        <th>Instructions</th>
        <th style="width:180px;">Actions</th>
    </tr>
    </thead>
    <tbody>
    @forelse($recipes as $r)
        <tr>
            <td>{{ $r->name }}</td>
            <td>{{ \Illuminate\Support\Str::limit($r->instructions, 160) }}</td>
            <td style="white-space:nowrap;">
                <a href="{{ route('recipes.show', $r) }}">Open</a>
                <a href="{{ route('recipes.edit', $r) }}" style="margin-left:8px;">Edit</a>
                <form method="post" action="{{ route('recipes.destroy', $r) }}" style="display:inline;margin-left:8px;">
                    @csrf @method('DELETE')
                    <button type="submit" onclick="return confirm('Delete this recipe?')">Del</button>
                </form>
            </td>
        </tr>
    @empty
        <tr><td colspan="3">No recipes yet.</td></tr>
    @endforelse
    </tbody>
</table>
@endsection
