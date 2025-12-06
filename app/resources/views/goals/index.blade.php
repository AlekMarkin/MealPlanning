@extends('layouts.app')

@section('content')
<div style="max-width:1200px; margin:0 auto; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0">Goals</h1>
        <a href="{{ route('goals.create') }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Add New Goal</a>
    </div>
    

    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border: 1px solid #c3e6cb; border-radius: 5px;">
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
            {{ session('error') }}
        </div>
    @endif

    <!-- Goals Table -->
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0;">My Goals</h2>

        @if($goals->isEmpty())
            <p style="text-align: center; color: #999; padding: 40px 0;">
                No goals yet. <a href="{{ route('goals.create') }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Create your first goal</a>
            </p>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left;">Metric</th>
                            <th style="padding: 12px; text-align: left;">Target</th>
                            <th style="padding: 12px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($goals as $g)
                            @php
                                $meta = $metrics[$g->metric] ?? null;
                                $label = is_array($meta) ? ($meta['label'] ?? $g->metric) : ($meta ?? $g->metric);
                                $unit  = (is_array($meta) && !empty($meta['unit'])) ? ' '.$meta['unit'] : '';
                                $target = rtrim(rtrim(number_format($g->target_value, 2, '.', ''), '0'), '.');
                            @endphp
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px;">{{ $label }}</td>
                                <td style="padding: 12px;">{{ $target }}{{ $unit }}</td>
                                <td style="padding: 12px; text-align: center; white-space: nowrap;">
                                    <a href="{{ route('goals.edit', $g->id) }}" style="display: inline-block; background-color: #007bff; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; font-size: 14px; margin-right: 8px;">Edit</a>
                                    <form method="post" action="{{ route('goals.destroy', $g->id) }}" style="display: inline;" onsubmit="return confirm('Delete this goal?');">
                                        @csrf
                                        <button type="submit" style="background-color: #dc3545; color: white; padding: 8px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">Delete</button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
        @endif
    </div>
</div>
@endsection