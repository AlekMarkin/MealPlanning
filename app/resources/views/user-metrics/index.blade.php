@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">Your Health Metrics</h1>
        <a href="{{ route('user-metrics.create') }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Add New Metric</a>
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

    <!-- Summary Cards -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; margin-bottom: 30px;">
        <!-- Latest Weight -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-top: 0; color: #666; font-size: 14px;">Latest Weight</h3>
            <p style="font-size: 32px; font-weight: bold; margin: 10px 0; color: #333;">
                {{ $latestMetric ? number_format($latestMetric->weight_kg, 1) : '-' }} <span style="font-size: 18px;">kg</span>
            </p>
            <p style="color: #999; font-size: 12px;">
                {{ $latestMetric ? date('M d, Y', strtotime($latestMetric->recorded_date)) : 'No data' }}
            </p>
        </div>

        <!-- Latest Blood Pressure -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-top: 0; color: #666; font-size: 14px;">Latest Blood Pressure</h3>
            <p style="font-size: 32px; font-weight: bold; margin: 10px 0; color: #333;">
                {{ $latestMetric ? $latestMetric->bp_systolic . '/' . $latestMetric->bp_diastolic : '-' }}
            </p>
            <p style="color: #999; font-size: 12px;">
                {{ $latestMetric ? date('M d, Y', strtotime($latestMetric->recorded_date)) : 'No data' }}
            </p>
        </div>

        <!-- 30-Day Average Weight -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-top: 0; color: #666; font-size: 14px;">30-Day Avg Weight</h3>
            <p style="font-size: 32px; font-weight: bold; margin: 10px 0; color: #333;">
                {{ $averages['weight'] ? number_format($averages['weight'], 1) : '-' }} <span style="font-size: 18px;">kg</span>
            </p>
            <p style="color: #999; font-size: 12px;">Last 30 days</p>
        </div>

        <!-- 30-Day Average BP -->
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-top: 0; color: #666; font-size: 14px;">30-Day Avg BP</h3>
            <p style="font-size: 32px; font-weight: bold; margin: 10px 0; color: #333;">
                {{ $averages['systolic'] && $averages['diastolic'] ? round($averages['systolic']) . '/' . round($averages['diastolic']) : '-' }}
            </p>
            <p style="color: #999; font-size: 12px;">Last 30 days</p>
        </div>
    </div>

    <!-- Metrics Chart -->
    <div style="background: white; padding: 20px; margin-bottom: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0;">Metrics Chart</h2>
        <canvas id="metricsChart" height="120"></canvas>
    </div>

    <!-- Metrics History Table -->
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h2 style="margin-top: 0;">Metrics History</h2>

        @if($metrics->isEmpty())
            <p style="text-align: center; color: #999; padding: 40px 0;">
                No metrics recorded yet. <a href="{{ route('user-metrics.create') }}">Add your first metric</a>
            </p>
        @else
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                            <th style="padding: 12px; text-align: left;">Date</th>
                            <th style="padding: 12px; text-align: left;">Weight (kg)</th>
                            <th style="padding: 12px; text-align: left;">Blood Pressure</th>
                            <th style="padding: 12px; text-align: center;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($metrics as $metric)
                            <tr style="border-bottom: 1px solid #dee2e6;">
                                <td style="padding: 12px;">{{ date('M d, Y', strtotime($metric->recorded_date)) }}</td>
                                <td style="padding: 12px;">{{ number_format($metric->weight_kg, 1) }}</td>
                                <td style="padding: 12px;">{{ $metric->bp_systolic }}/{{ $metric->bp_diastolic }}</td>
                                <td style="padding: 12px; text-align: center; white-space: nowrap;">
                                    <a href="{{ route('user-metrics.edit', $metric->id) }}" style="display: inline-block; background-color: #007bff; color: white; padding: 8px 12px; text-decoration: none; border-radius: 4px; font-size: 14px; margin-right: 8px;">Edit</a>
                                    <form action="{{ route('user-metrics.destroy', $metric->id) }}" method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this metric?');">
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

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function() {

    const labels = {!! json_encode($metrics->pluck('recorded_date')->map(fn($d) => date('M d Y', strtotime($d)))) !!};
    const weights = {!! json_encode($metrics->pluck('weight_kg')) !!};
    const systolic = {!! json_encode($metrics->pluck('bp_systolic')) !!};
    const diastolic = {!! json_encode($metrics->pluck('bp_diastolic')) !!};

    const ctx = document.getElementById('metricsChart').getContext('2d');

    new Chart(ctx, {
        type: 'line',
        data: {
            labels: labels,
            datasets: [
                {
                    label: 'Weight (kg)',
                    data: weights,
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.2)',
                    borderWidth: 2,
                    tension: 0.3,
                    yAxisID: 'y'
                },
                {
                    label: 'Systolic (BP)',
                    data: systolic,
                    borderColor: '#dc3545',
                    backgroundColor: 'rgba(220, 53, 69, 0.2)',
                    borderWidth: 2,
                    tension: 0.3,
                    yAxisID: 'y1'
                },
                {
                    label: 'Diastolic (BP)',
                    data: diastolic,
                    borderColor: '#6f42c1',
                    backgroundColor: 'rgba(111, 66, 193, 0.2)',
                    borderWidth: 2,
                    tension: 0.3,
                    yAxisID: 'y1'
                }
            ]
        },
        options: {
            responsive: true,
            interaction: {
                intersect: false,
                mode: 'index'
            },
            scales: {
                y: {
                    type: 'linear',
                    position: 'left',
                    title: { display: true, text: 'Weight (kg)' }
                },
                y1: {
                    type: 'linear',
                    position: 'right',
                    title: { display: true, text: 'Blood Pressure' },
                    grid: { drawOnChartArea: false }
                }
            }
        }
    });
});
</script>

@endsection