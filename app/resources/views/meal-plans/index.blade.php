@extends('layouts.app')

@section('content')
<div style="max-width: 1200px; margin: 0 auto; padding: 20px;">
    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 30px;">
        <h1 style="margin: 0;">Your Meal Plans</h1>
        <a href="{{ route('meal-plans.create') }}" style="background-color: #4CAF50; color: white; padding: 10px 20px; text-decoration: none; border-radius: 5px;">Add new meal plan</a>
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

    @if($groupedPlans->isEmpty())
        <div style="background: white; padding: 40px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); text-align: center; color: #999;">
            No meal plans yet. <a href="{{ route('meal-plans.create') }}">Create your first meal plan</a>
        </div>
    @else
        @foreach($groupedPlans as $date => $plans)
            <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
                <h2 style="margin-top: 0; color: #333; border-bottom: 2px solid #f0f0f0; padding-bottom: 10px;">
                    {{ date('l, F j, Y', strtotime($date)) }}
                </h2>

                <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 15px; margin-top: 20px;">
                    @foreach($plans as $plan)
                        <div style="border: 1px solid #e0e0e0; border-radius: 8px; padding: 15px; background: #fafafa;">
                            <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 10px;">
                                <span style="background-color: 
                                    @if($plan->meal_type == 'breakfast') #FFE5B4
                                    @elseif($plan->meal_type == 'lunch') #E6F3FF
                                    @elseif($plan->meal_type == 'dinner') #FFE6E6
                                    @else #E6FFE6
                                    @endif
                                ; padding: 5px 10px; border-radius: 12px; font-size: 12px; font-weight: bold; text-transform: uppercase;">
                                    {{ $plan->meal_type }}
                                </span>
                                
                                @if($plan->custom_food_name)
                                    <span style="background-color: #FFF3CD; color: #856404; padding: 5px 10px; border-radius: 12px; font-size: 11px; font-weight: bold;">
                                        CUSTOM
                                    </span>
                                @endif
                            </div>

                            <h3 style="margin: 10px 0; color: #333; font-size: 18px;">
                                {{ $plan->custom_food_name ?? $plan->recipe_name }}
                            </h3>
                            
                            @if($plan->recipe_description && !$plan->custom_food_name)
                                <p style="color: #666; font-size: 14px; margin: 10px 0;">
                                    {{ Str::limit($plan->recipe_description, 80) }}
                                </p>
                            @endif

                            <div style="display: flex; gap: 10px; margin-top: 15px;">
                                <a href="{{ route('meal-plans.edit', $plan->id) }}" style="flex: 1; text-align: center; background-color: #007bff; color: white; padding: 8px; text-decoration: none; border-radius: 4px; font-size: 14px;">
                                    Edit
                                </a>
                                <form action="{{ route('meal-plans.destroy', $plan->id) }}" method="POST" style="flex: 1;" onsubmit="return confirm('Are you sure you want to delete this meal plan?');">
                                    @csrf
                                    <button type="submit" style="width: 100%; background-color: #dc3545; color: white; padding: 8px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">
                                        Delete
                                    </button>
                                </form>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endforeach
    @endif
</div>
@endsection
