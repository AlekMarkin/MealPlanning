@extends('layouts.app')

@section('content')
<div style="max-width:880px;margin:0 auto;text-align:left;">
    <h1 style="margin-bottom:12px;">Goals</h1>

    @if(session('success'))
        <div style="background:#e6ffed;border:1px solid #b7eb8f;padding:8px;margin-bottom:10px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background:#ffecec;border:1px solid #f5aca6;padding:8px;margin-bottom:10px;">
            {{ session('error') }}
        </div>
    @endif

    <div style="margin-bottom:10px;">
        <a href="{{ route('goals.create') }}">+ New Goal</a>
    </div>

    <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>Metric</th>
            <th>Target</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse($goals as $g)
            @php
                $meta = $metrics[$g->metric] ?? null;
                // Support either ['label'=>'…','unit'=>'…'] or a plain string label
                $label = is_array($meta) ? ($meta['label'] ?? $g->metric) : ($meta ?? $g->metric);
                $unit  = (is_array($meta) && !empty($meta['unit'])) ? ' '.$meta['unit'] : '';
                $target = rtrim(rtrim(number_format($g->target_value, 2, '.', ''), '0'), '.');
            @endphp
            <tr>
                <td>{{ $label }}</td>
                <td>{{ $target }}{{ $unit }}</td>
                <td style="white-space:nowrap;">
                    <a href="{{ route('goals.edit', $g->id) }}">Edit</a>
                    <form method="post" action="{{ route('goals.destroy', $g->id) }}" style="display:inline;">
                        @csrf
                        <button type="submit" onclick="return confirm('Delete this goal?')">Del</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="3">No goals yet.</td></tr>
        @endforelse
        </tbody>
    </table>
</div>
@endsection
