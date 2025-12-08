@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="margin-bottom: 30px;">Edit Goal</h1>

    {{-- session error message --}}
    @if(session('error'))
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
            {{ session('error') }}
        </div>
    @endif

    {{-- validation errors --}}
    @if ($errors->any())
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
            <b>Fix the following:</b>
            <ul style="margin: 6px 0 0 20px;">
                @foreach ($errors->all() as $e)
                    <li>{{ $e }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- goal edit form --}}
    <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <form method="POST" action="{{ route('goals.update', $goal->id) }}">
            @csrf
            @method('PUT')

            {{-- metric selection dropdown --}}
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Metric</label>
                <select name="metric" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    @foreach($metrics as $key => $meta)
                        <option value="{{ $key }}" {{ old('metric', $goal->metric) === $key ? 'selected' : '' }}>
                            {{ $meta['label'] }} ({{ $meta['unit'] }})
                        </option>
                    @endforeach
                </select>
            </div>

            {{-- target value input --}}
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Target Value</label>
                <input type="number" name="target_value" step="0.01" min="0" value="{{ old('target_value', $goal->target_value) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;">
            </div>

            {{-- period selection (daily, weekly, monthly) --}}
            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Period</label>
                <select name="period" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    <option value="daily" {{ old('period', $goal->period ?? 'daily') === 'daily' ? 'selected' : '' }}>Daily</option>
                    <option value="weekly" {{ old('period', $goal->period ?? 'daily') === 'weekly' ? 'selected' : '' }}>Weekly</option>
                    <option value="monthly" {{ old('period', $goal->period ?? 'daily') === 'monthly' ? 'selected' : '' }}>Monthly</option>
                </select>
                <p style="color: #666; font-size: 12px; margin-top: 5px;">Daily goals will be shown in Daily Intake progress.</p>
            </div>

            {{-- form action buttons --}}
            <div style="display: flex; gap: 10px; margin-top: 30px;">
                <button type="submit" style="flex: 1; background-color: #4CAF50; color: white; padding: 12px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; font-weight: bold;">Update Goal</button>
                <a href="{{ route('goals.index') }}" style="flex: 1; text-align: center; background-color: #6c757d; color: white; padding: 12px; text-decoration: none; border-radius: 5px; font-size: 16px; font-weight: bold;">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection