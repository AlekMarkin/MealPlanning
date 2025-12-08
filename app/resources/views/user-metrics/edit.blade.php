@extends('layouts.app')

@section('content')
<div style="max-width: 600px; margin: 0 auto; padding: 20px;">
    <h1 style="margin-bottom: 30px;">Edit Health Metric</h1>

    {{-- error message --}}
    @if(session('error'))
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
            {{ session('error') }}
        </div>
    @endif

    {{-- validation errors --}}
    @if($errors->any())
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
            <ul style="margin: 0; padding-left: 20px;">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    {{-- metric edit form --}}
    <form action="{{ route('user-metrics.update', $metric->id) }}" method="POST" style="background: white; padding: 30px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        @csrf
        @method('PUT')

        {{-- date input --}}
        <div style="margin-bottom: 20px;">
            <label for="recorded_date" style="display: block; margin-bottom: 5px; font-weight: bold;">Date</label>
            <input type="date" 
                   id="recorded_date" 
                   name="recorded_date" 
                   value="{{ old('recorded_date', $metric->recorded_date) }}" 
                   required
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
        </div>

        {{-- weight input --}}
        <div style="margin-bottom: 20px;">
            <label for="weight_kg" style="display: block; margin-bottom: 5px; font-weight: bold;">Weight (kg)</label>
            <input type="number" 
                   id="weight_kg" 
                   name="weight_kg" 
                   step="0.1" 
                   min="0" 
                   max="500" 
                   value="{{ old('weight_kg', $metric->weight_kg) }}" 
                   required
                   placeholder="70.5"
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            <small style="color: #666;">Enter your weight in kilograms</small>
        </div>

        {{-- systolic blood pressure input --}}
        <div style="margin-bottom: 20px;">
            <label for="bp_systolic" style="display: block; margin-bottom: 5px; font-weight: bold;">Systolic Blood Pressure</label>
            <input type="number" 
                   id="bp_systolic" 
                   name="bp_systolic" 
                   min="50" 
                   max="300" 
                   value="{{ old('bp_systolic', $metric->bp_systolic) }}" 
                   required
                   placeholder="120"
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            <small style="color: #666;">Upper number (e.g., 120)</small>
        </div>

        {{-- diastolic blood pressure input --}}
        <div style="margin-bottom: 30px;">
            <label for="bp_diastolic" style="display: block; margin-bottom: 5px; font-weight: bold;">Diastolic Blood Pressure</label>
            <input type="number" 
                   id="bp_diastolic" 
                   name="bp_diastolic" 
                   min="30" 
                   max="200" 
                   value="{{ old('bp_diastolic', $metric->bp_diastolic) }}" 
                   required
                   placeholder="80"
                   style="width: 100%; padding: 10px; border: 1px solid #ddd; border-radius: 4px; font-size: 16px;">
            <small style="color: #666;">Lower number (e.g., 80)</small>
        </div>

        {{-- form action buttons --}}
        <div style="display: flex; gap: 10px;">
            <button type="submit" style="flex: 1; background-color: #4CAF50; color: white; padding: 12px; border: none; border-radius: 5px; font-size: 16px; cursor: pointer;">
                Update Metric
            </button>
            <a href="{{ route('user-metrics.index') }}" style="flex: 1; background-color: #6c757d; color: white; padding: 12px; border: none; border-radius: 5px; font-size: 16px; text-decoration: none; text-align: center; display: block;">
                Cancel
            </a>
        </div>
    </form>
</div>
@endsection