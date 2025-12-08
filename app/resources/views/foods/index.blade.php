@extends('layouts.app')

@section('content')
<div style="max-width: 980px; margin: 0 auto; padding: 0 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
        <h1 style="margin: 0;">Foods</h1>
        @if(session('user_id'))
            <a href="{{ route('foods.create') }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">+ New Food</a>
        @endif
    </div>

    @if(session('ok'))
        <div style="background: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border: 1px solid #c3e6cb; border-radius: 5px;">
            {{ session('ok') }}
        </div>
    @endif

    @if(session('error'))
        <div style="background: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
            {{ session('error') }}
        </div>
    @endif

    <div style="display: flex; gap: 8px; align-items: center; margin-bottom: 12px;">
        <form method="get" action="{{ route('foods.index') }}" style="display: flex; gap: 8px;">
            <input type="text" name="q" value="{{ old('q', $q ?? request('q')) }}" placeholder="Search name..." style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;" />
            <button type="submit" style="background-color: #007bff; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer;">Search</button>
        </form>
    </div>

    <div style="overflow-x: auto;">
        <table style="width: 100%; border-collapse: collapse; background: white;">
            <thead>
            <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                <th style="padding: 12px; text-align: left;">Name</th>
                <th style="padding: 12px; text-align: left;">kcal/100g</th>
                <th style="padding: 12px; text-align: left;">Protein (g)</th>
                <th style="padding: 12px; text-align: left;">Carbs (g)</th>
                <th style="padding: 12px; text-align: left;">Fat (g)</th>
                <th style="padding: 12px; text-align: left;">Fiber (g)</th>
                <th style="padding: 12px; text-align: left;">Sugar (g)</th>
                <th style="padding: 12px; text-align: left;">Sodium (mg)</th>
                <th style="padding: 12px; text-align: left;">gCO2e/100g</th>
                <th style="padding: 12px; text-align: center;">Actions</th>
            </tr>
            </thead>
            <tbody>
            @forelse($foods as $f)
                <tr style="border-bottom: 1px solid #dee2e6;">
                    <td style="padding: 12px;">{{ $f->name }}</td>
                    <td style="padding: 12px;">{{ $f->calories }}</td>
                    <td style="padding: 12px;">{{ $f->protein }}</td>
                    <td style="padding: 12px;">{{ $f->carbs }}</td>
                    <td style="padding: 12px;">{{ $f->fat }}</td>
                    <td style="padding: 12px;">{{ $f->fiber }}</td>
                    <td style="padding: 12px;">{{ $f->sugar }}</td>
                    <td style="padding: 12px;">{{ $f->sodium_mg }}</td>
                    <td style="padding: 12px;">{{ $f->carbon_footprint_gco2e }}</td>
                    <td style="padding: 12px; text-align: center; white-space: nowrap;">
                        @if(session('user_id'))
                            <a href="{{ route('foods.edit', $f) }}" style="display: inline-block; background-color: #007bff; color: white; padding: 6px 12px; text-decoration: none; border-radius: 4px; font-size: 13px; margin-right: 8px;">Edit</a>
                            <form method="post" action="{{ route('foods.destroy', $f) }}" style="display: inline;" onsubmit="return confirm('Delete this food?');">
                                @csrf
                                @method('DELETE')
                                <button type="submit" style="background-color: #dc3545; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">Delete</button>
                            </form>
                        @else
                            <span style="color: #999;">-</span>
                        @endif
                    </td>
                </tr>
            @empty
                <tr><td colspan="10" style="padding: 12px; text-align: center; color: #999;">No foods yet.</td></tr>
            @endforelse
            </tbody>
        </table>
    </div>

    <div style="margin-top: 10px;">
        @if(method_exists($foods, 'links') && $foods->hasPages())
            {{ $foods->appends(request()->query())->render('pagination') }}
        @endif
    </div>
</div>
@endsection