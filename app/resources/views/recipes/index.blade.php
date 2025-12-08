@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    {{-- page header with add button --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">Recipes</h1>
        @if(session('user_id'))
            <a href="{{ route('recipes.create') }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">+ New Recipe</a>
        @endif
    </div>

    {{-- success message --}}
    @if(session('ok'))
        <div style="background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border: 1px solid #c3e6cb; border-radius: 5px;">
            {{ session('ok') }}
        </div>
    @endif

    {{-- error message --}}
    @if(session('error'))
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
            {{ session('error') }}
        </div>
    @endif

    {{-- search section --}}
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 30px;">
        <form method="get" action="{{ route('recipes.index') }}" style="display: flex; gap: 10px; align-items: center;">
            <input type="text" name="q" value="{{ old('q', $q ?? request('q')) }}" placeholder="Search recipe name..." style="flex: 1; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;" />
            <button type="submit" style="background-color: #007bff; color: white; padding: 10px 20px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">Search</button>
        </form>
    </div>

    {{-- recipes table --}}
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0; margin-bottom: 20px;">All Recipes</h2>

        {{-- empty state --}}
        @if($recipes->isEmpty())
            <p style="text-align: center; color: #999; padding: 40px 0;">
                No recipes found.
                @if(session('user_id'))
                    <a href="{{ route('recipes.create') }}">Create your first recipe</a>
                @endif
            </p>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    {{-- table header --}}
                    <thead>
                        <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left;">Name</th>
                            <th style="padding: 12px; text-align: left;">Instructions</th>
                            <th style="padding: 12px; text-align: left;">Created</th>
                            <th style="padding: 12px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    {{-- table body with recipe rows --}}
                    <tbody>
                        @foreach($recipes as $r)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px;"><a href="{{ route('recipes.show', $r) }}" style="color: #007bff; text-decoration: none;">{{ $r->name }}</a></td>
                                <td style="padding: 12px;">{{ Str::limit($r->instructions, 60) ?? '–' }}</td>
                                <td style="padding: 12px;">{{ $r->created_at ? $r->created_at->format('Y-m-d') : '–' }}</td>
                                {{-- edit and delete actions --}}
                                <td style="padding: 12px; text-align: center; white-space: nowrap;">
                                    @if(session('user_id'))
                                        <a href="{{ route('recipes.edit', $r) }}" style="display: inline-block; background-color: #007bff; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; font-size: 14px; margin-right: 8px;">Edit</a>
                                        <form method="post" action="{{ route('recipes.destroy', $r) }}" style="display: inline;" onsubmit="return confirm('Delete this recipe?');">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" style="background-color: #dc3545; color: white; padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">Delete</button>
                                        </form>
                                    @else
                                        <span style="color: #999;">-</span>
                                    @endif
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            {{-- pagination --}}
            @if(method_exists($recipes, 'links') && $recipes->hasPages())
                <div style="margin-top: 20px; display: flex; justify-content: center;">
                    {{ $recipes->appends(request()->query())->render('pagination') }}
                </div>
            @endif
        @endif
    </div>
</div>
@endsection