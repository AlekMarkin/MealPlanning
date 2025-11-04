@extends('layouts.app')

@section('content')
<div style="max-width:620px;margin:0 auto;text-align:left;">
    <h1 style="margin-bottom:12px;">New Goal</h1>

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

    <form method="post" action="{{ route('goals.store') }}">
        @csrf

        <label>Metric</label><br>
        <select name="metric" required>
            <option value="">— select —</option>
            @foreach($metrics as $key => $meta)
                <option value="{{ $key }}" {{ old('metric')===$key?'selected':'' }}>
                    {{ $meta['label'] }}
                </option>
            @endforeach
        </select>
        <br><br>

        <label>Target value</label><br>
        <input type="number" step="0.01" min="0" name="target_value" value="{{ old('target_value') }}" required />
        <br><br>

        <label>Period</label><br>
        <select name="period" required>
            @foreach(['daily','weekly','monthly'] as $p)
                <option value="{{ $p }}" {{ old('period','daily')===$p?'selected':'' }}>
                    {{ ucfirst($p) }}
                </option>
            @endforeach
        </select>
        <br><br>

        <button type="submit">Save Goal</button>
        <a href="{{ route('goals.index') }}" style="margin-left:8px;">Cancel</a>
    </form>
</div>
@endsection
