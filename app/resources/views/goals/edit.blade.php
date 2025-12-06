@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">Edit Goal</h1>
        <a href="{{ route('goals.index') }}" style="background-color: #6c757d; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Back to Goals</a>
    </div>

    @if(session('error'))
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
            {{ session('error') }}
        </div>
    @endif

    @if($errors->any())
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    <div style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <form method="post" action="{{ route('goals.update', $goal->id) }}">
            @csrf

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Metric</label>
                <select name="metric" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    @foreach($metrics as $key => $meta)
                        <option value="{{ $key }}" {{ old('metric',$goal->metric)===$key?'selected':'' }}>
                            {{ $meta['label'] }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="margin-bottom: 20px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Target value</label>
                <input type="number" step="0.01" min="0" name="target_value" value="{{ old('target_value',$goal->target_value) }}" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px; box-sizing: border-box;" />
            </div>

            <div style="margin-bottom: 30px;">
                <label style="display: block; margin-bottom: 8px; font-weight: bold; color: #333;">Period</label>
                <select name="period" required style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                    @foreach(['daily','weekly','monthly'] as $p)
                        <option value="{{ $p }}" {{ old('period',$goal->period)===$p?'selected':'' }}>
                            {{ ucfirst($p) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div style="display: flex; gap: 10px;">
                <button type="submit" style="flex: 1; background-color: #4CAF50; color: white; padding: 12px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer; font-weight: bold;">Update Goal</button>
                <a href="{{ route('goals.index') }}" style="flex: 1; text-align: center; background-color: #6c757d; color: white; padding: 12px; text-decoration: none; border-radius: 5px; font-size: 16px; font-weight: bold;">Cancel</a>
            </div>
        </form>
    </div>
</div>
@endsection