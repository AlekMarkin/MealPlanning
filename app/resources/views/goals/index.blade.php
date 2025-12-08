@extends('layouts.app')

@section('content')
<div style="max-width: 900px; margin: 0 auto; padding: 20px;">
    {{-- page header with add button --}}
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">Your Goals</h1>
        <a href="{{ route('goals.create') }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Add New Goal</a>
    </div>

    {{-- success message --}}
    @if(session('success'))
        <div style="background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border: 1px solid #c3e6cb; border-radius: 5px;">
            {{ session('success') }}
        </div>
    @endif

    {{-- error message --}}
    @if(session('error'))
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
            {{ session('error') }}
        </div>
    @endif

    {{-- empty state when no goals exist --}}
    @if($goals->isEmpty())
        <div style="background: white; padding: 40px; text-align: center; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <p style="color: #666; margin-bottom: 20px;">You haven't set any goals yet.</p>
            <a href="{{ route('goals.create') }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Create Your First Goal</a>
        </div>
    @else
        {{-- goals table --}}
        <div style="background: white; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); overflow: hidden;">
            <table style="width: 100%; border-collapse: collapse;">
                {{-- table header --}}
                <thead>
                    <tr style="background-color: #f8f9fa;">
                        <th style="padding: 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Metric</th>
                        <th style="padding: 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Target</th>
                        <th style="padding: 15px; text-align: left; border-bottom: 2px solid #dee2e6;">Period</th>
                        <th style="padding: 15px; text-align: center; border-bottom: 2px solid #dee2e6;">Actions</th>
                    </tr>
                </thead>
                {{-- table body with goal rows --}}
                <tbody>
                    @foreach($goals as $g)
                        @php
                            $meta = $metrics[$g->metric] ?? ['label' => $g->metric, 'unit' => ''];
                        @endphp
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 15px;">{{ $meta['label'] }}</td>
                            <td style="padding: 15px;">{{ number_format($g->target_value, 1) }} {{ $meta['unit'] }}</td>
                            {{-- period badge with color coding --}}
                            <td style="padding: 15px;">
                                <span style="
                                    display: inline-block;
                                    padding: 4px 10px;
                                    border-radius: 12px;
                                    font-size: 12px;
                                    font-weight: bold;
                                    @if(($g->period ?? 'daily') === 'daily')
                                        background-color: #d4edda; color: #155724;
                                    @elseif(($g->period ?? 'daily') === 'weekly')
                                        background-color: #cce5ff; color: #004085;
                                    @else
                                        background-color: #fff3cd; color: #856404;
                                    @endif
                                ">
                                    {{ ucfirst($g->period ?? 'daily') }}
                                </span>
                            </td>
                            {{-- edit and delete actions --}}
                            <td style="padding: 15px; text-align: center;">
                                <a href="{{ route('goals.edit', $g->id) }}" style="color: #007bff; text-decoration: none; margin-right: 15px;">Edit</a>
                                <form action="{{ route('goals.destroy', $g->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Delete this goal?');">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" style="color: #dc3545; background: none; border: none; cursor: pointer; font-size: inherit;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    @endif
</div>
@endsection