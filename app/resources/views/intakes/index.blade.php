@extends('layouts.app')

@section('content')
<div style="max-width: 960px; margin: 0 auto; padding: 20px; text-align: left;">
    <h1 style="margin-bottom: 20px; text-align: left;">Daily Intake</h1>

    {{-- success message --}}
    @if(session('ok'))
        <div style="background-color: #d4edda; color: #155724; padding: 15px; margin-bottom: 20px; border: 1px solid #c3e6cb; border-radius: 5px;">
            {{ session('ok') }}
        </div>
    @endif

    {{-- error message --}}
    @if(session('error'))
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
            {{ session('error') }}
        </div>
    @endif

    {{-- validation errors --}}
    @if($errors->any())
        <div style="background-color: #f8d7da; color: #721c24; padding: 15px; margin-bottom: 20px; border: 1px solid #f5c6cb; border-radius: 5px;">
            <ul style="margin: 0; padding-left: 18px;">
                @foreach($errors->all() as $e)<li>{{ $e }}</li>@endforeach
            </ul>
        </div>
    @endif

    {{-- Date picker --}}
    <form method="get" action="{{ route('intakes.index') }}" style="display:flex;gap:10px;align-items:center;margin-bottom:20px;justify-content:flex-start;">
        <label for="date" style="white-space: nowrap;"><strong>Date:</strong></label>
        <input id="date" type="date" name="date" value="{{ $date }}" style="padding: 8px; border: 1px solid #ddd; border-radius: 4px;" />
        <button type="submit" style="background-color: #007bff; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">Go</button>
        <button type="button" onclick="window.location='{{ route('intakes.index') }}'" style="background-color: #6c757d; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">Today</button>
    </form>

    {{-- Add Food --}}
    <fieldset style="border: none; background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <legend style="font-weight: bold; margin-bottom: 15px; padding: 0; text-align: left;">Add Food</legend>
        <form method="post" action="{{ route('intakes.food.store') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;justify-content:flex-start;">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <select name="food_id" required style="flex:1;min-width:150px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                <option value="">– choose food –</option>
                @foreach($foods as $f)
                    <option value="{{ $f->id }}">{{ $f->name }}</option>
                @endforeach
            </select>
            <input type="number" name="grams" min="0" step="1" value="100" placeholder="grams (g)" style="width:100px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;" />
            <input type="time" name="time" placeholder="Time" style="width:120px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;" id="foodTime" />
            <button type="submit" style="background-color: #4CAF50; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">Add</button>
            <a href="{{ route('foods.create') }}" style="background-color: #28a745; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; text-decoration: none;">+ New Food</a>
        </form>
    </fieldset>

    {{-- Add Recipe --}}
    <fieldset style="border: none; background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <legend style="font-weight: bold; margin-bottom: 15px; padding: 0; text-align: left;">Add Recipe</legend>
        <form method="post" action="{{ route('intakes.recipe.store') }}" style="display:flex;gap:10px;align-items:center;flex-wrap:wrap;justify-content:flex-start;">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <select name="recipe_id" required style="flex:1;min-width:150px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;">
                <option value="">– choose recipe –</option>
                @foreach($recipes as $r)
                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                @endforeach
            </select>
            <input type="number" name="servings" min="0" step="0.25" value="1" placeholder="servings" style="width:100px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;" />
            <input type="time" name="time" placeholder="Time" style="width:120px; padding: 8px; border: 1px solid #ddd; border-radius: 4px; font-size: 14px;" id="recipeTime" />
            <button type="submit" style="background-color: #4CAF50; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px;">Add</button>
            <a href="{{ route('recipes.create') }}" style="background-color: #28a745; color: white; padding: 8px 15px; border: none; border-radius: 4px; cursor: pointer; font-size: 14px; text-decoration: none;">+ New Recipe</a>
        </form>
    </fieldset>

    {{-- Charts Section --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        
        {{-- Nutrients --}}
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-top:0; margin-bottom:15px;">Nutrients</h3>
            <div style="position: relative; height: 300px;">
                <canvas id="goalsChart"></canvas>
            </div>
        </div>

        {{-- Carbon Footprint --}}
        <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
            <h3 style="margin-top:0; margin-bottom:15px;">Carbon Footprint</h3>
            <div style="position: relative; height: 300px;">
                <canvas id="carbonChart"></canvas>
            </div>
        </div>

    </div>

    {{-- Goal Progress Indicators --}}
    <div style="background: white; padding: 20px; margin-bottom: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3 style="margin-top:0; margin-bottom:15px;">Daily Goal Progress</h3>
        <div id="goalIndicators" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
        </div>
    </div>

    {{-- Intakes table --}}
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1); margin-bottom: 20px;">
        <h3 style="margin-top: 0; margin-bottom: 15px;">Entries for {{ $date }}</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left;">Type</th>
                        <th style="padding: 12px; text-align: left;">Name</th>
                        <th style="padding: 12px; text-align: left;">Qty</th>
                        <th style="padding: 12px; text-align: left;">When</th>
                        <th style="padding: 12px; text-align: center;">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($intakes as $in)
                        <tr style="border-bottom: 1px solid #dee2e6;">
                            <td style="padding: 12px;">{{ $in->food_id ? 'Food' : 'Recipe' }}</td>
                            <td style="padding: 12px;">
                                @if($in->food_id)
                                    {{ optional(\App\Models\Food::find($in->food_id))->name ?? '–' }}
                                @elseif($in->recipe_id)
                                    {{ optional(\App\Models\Recipe::find($in->recipe_id))->name ?? '–' }}
                                @else
                                    –
                                @endif
                            </td>
                            <td style="padding: 12px;">
                                @if($in->food_id)
                                    {{ (int)$in->quantity_g }} g
                                @elseif($in->recipe_id)
                                    {{ (float)$in->servings }} serving(s)
                                @endif
                            </td>
                            <td style="padding: 12px;">
                                {{ $in->consumed_at ? \Illuminate\Support\Carbon::parse($in->consumed_at)->format('H:i') : '–' }}
                            </td>
                            <td style="padding: 12px; text-align: center;">
                                <form method="post" action="{{ route('intakes.destroy', $in) }}" style="display:inline;" onsubmit="return confirm('Remove this entry?');">
                                    @csrf @method('DELETE')
                                    <button type="submit" style="background-color: #dc3545; color: white; padding: 6px 12px; border: none; border-radius: 4px; cursor: pointer; font-size: 13px;">Delete</button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr><td colspan="5" style="padding: 12px; text-align: center; color: #999;">No entries yet.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    {{-- Totals --}}
    <div style="background: white; padding: 20px; border-radius: 8px; box-shadow: 0 2px 4px rgba(0,0,0,0.1);">
        <h3 style="margin-top: 0; margin-bottom: 15px;">Totals</h3>
        <div style="overflow-x: auto;">
            <table style="width: 100%; border-collapse: collapse;">
                <thead>
                    <tr style="background-color: #f8f9fa; border-bottom: 2px solid #dee2e6;">
                        <th style="padding: 12px; text-align: left;">kcal</th>
                        <th style="padding: 12px; text-align: left;">Protein</th>
                        <th style="padding: 12px; text-align: left;">Carbs</th>
                        <th style="padding: 12px; text-align: left;">Fat</th>
                        <th style="padding: 12px; text-align: left;">Fiber</th>
                        <th style="padding: 12px; text-align: left;">Sugar</th>
                        <th style="padding: 12px; text-align: left;">Sodium (mg)</th>
                        <th style="padding: 12px; text-align: left;">Carbon (gCO2e)</th>
                    </tr>
                </thead>
                <tbody>
                    <tr style="border-bottom: 1px solid #dee2e6;">
                        <td style="padding: 12px;">{{ round($totals['calories'], 2) }}</td>
                        <td style="padding: 12px;">{{ round($totals['protein'], 2) }}</td>
                        <td style="padding: 12px;">{{ round($totals['carbs'], 2) }}</td>
                        <td style="padding: 12px;">{{ round($totals['fat'], 2) }}</td>
                        <td style="padding: 12px;">{{ round($totals['fiber'], 2) }}</td>
                        <td style="padding: 12px;">{{ round($totals['sugar'], 2) }}</td>
                        <td style="padding: 12px;">{{ round($totals['sodium_mg'], 2) }}</td>
                        <td style="padding: 12px;">{{ round($totals['carbon_footprint'], 2) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        //daily totals from controller
        var totals = {
            calories: {{ $totals['calories'] ?? 0 }},
            protein: {{ $totals['protein'] ?? 0 }},
            carbs: {{ $totals['carbs'] ?? 0 }},
            fat: {{ $totals['fat'] ?? 0 }},
            fiber: {{ $totals['fiber'] ?? 0 }},
            sugar: {{ $totals['sugar'] ?? 0 }},
            sodium: {{ $totals['sodium_mg'] ?? 0 }},
            carbon: {{ $totals['carbon_footprint'] ?? 0 }}
        };

        //user's daily goals from controller
        var goals = {
            calories: {{ $goals['calories'] ?? 'null' }},
            protein: {{ $goals['protein'] ?? 'null' }},
            carbs: {{ $goals['carbs'] ?? 'null' }},
            fat: {{ $goals['fat'] ?? 'null' }},
            fiber: {{ $goals['fiber'] ?? 'null' }},
            sugar: {{ $goals['sugar'] ?? 'null' }},
            sodium: {{ $goals['sodium'] ?? 'null' }},
            carbon: {{ $goals['carbon_footprint'] ?? 'null' }}
        };

        var goalLabels = {
            calories: 'Calories',
            protein: 'Protein',
            carbs: 'Carbs',
            fat: 'Fat',
            fiber: 'Fiber',
            sugar: 'Sugar',
            sodium: 'Sodium',
            carbon: 'Carbon'
        };

        //creates progress bar data for a goal metric
        function createProgressBar(metric, consumed, goal) {
            if (!goal) return null;
            
            var percentage = Math.min(100, (consumed / goal) * 100);
            var status = consumed <= goal ? 'under' : 'over';
            var barColor = consumed <= goal ? '#4CAF50' : '#FF6384';
            var backgroundColor = consumed <= goal ? '#E8F5E9' : '#FFEBEE';
            
            return {
                metric: metric,
                consumed: consumed.toFixed(1),
                goal: goal.toFixed(1),
                percentage: percentage.toFixed(0),
                status: status,
                barColor: barColor,
                backgroundColor: backgroundColor
            };
        }

        var goalsCtx = document.getElementById('goalsChart').getContext('2d');
        
        //doughnut chart with nutrients only
        var nutrientLabels = ['Protein', 'Carbs', 'Fat'];
        var nutrientData = [
            totals.protein,
            totals.carbs,
            totals.fat
        ];
        
        var colors = ['#36A2EB', '#FFCE56', '#FF6384'];
        var backgroundColors = [];
        var borderColors = [];
        
        //checks if there's any nutrient data
        var hasNutrientData = totals.protein > 0 || totals.carbs > 0 || totals.fat > 0;
        
        if (!hasNutrientData) {
            //shows empty state with grey circle
            nutrientLabels.push('Empty');
            nutrientData.push(100);
            colors.push('#E8E8E8');
        }
        
        for (var i = 0; i < nutrientLabels.length; i++) {
            backgroundColors.push(colors[i]);
            borderColors.push('#fff');
        }
        
        new Chart(goalsCtx, {
            type: 'doughnut',
            data: {
                labels: nutrientLabels,
                datasets: [
                    {
                        data: nutrientData,
                        backgroundColor: backgroundColors,
                        borderColor: borderColors,
                        borderWidth: 2
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                var label = context.label || '';
                                var value = context.parsed || 0;
                                return label + ': ' + value.toFixed(1);
                            }
                        }
                    }
                }
            }
        });

        //carbon footprint doughnut chart
        var carbonCtx = document.getElementById('carbonChart').getContext('2d');
        var carbonGoal = goals.carbon || 1000;

        new Chart(carbonCtx, {
            type: 'doughnut',
            data: {
                labels: ['Consumed', 'Remaining'],
                datasets: [{
                    data: [totals.carbon, Math.max(0, carbonGoal - totals.carbon)],
                    backgroundColor: [
                        '#FF6384',
                        '#E8E8E8'
                    ],
                    borderColor: '#fff',
                    borderWidth: 2
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom'
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                if (context.label === 'Consumed') {
                                    return 'Today: ' + context.parsed.toFixed(1) + ' gCO2e';
                                } else {
                                    return 'Estimate remaining';
                                }
                            }
                        }
                    }
                }
            }
        });

        //generates goal progress indicator bars
        var indicatorsContainer = document.getElementById('goalIndicators');
        var hasAnyGoal = false;

        Object.keys(goals).forEach(function(key) {
            if (goals[key] !== null && key !== 'carbon') {
                hasAnyGoal = true;
                var progress = createProgressBar(goalLabels[key], totals[key], goals[key]);
                
                var html = '<div style="background: ' + progress.backgroundColor + '; padding: 15px; border-radius: 4px; border-left: 4px solid ' + progress.barColor + ';">' +
                    '<div style="font-weight: bold; margin-bottom: 8px;">' + progress.metric + '</div>' +
                    '<div style="margin-bottom: 8px; font-size: 12px; color: #666;">' +
                    progress.consumed + ' / ' + progress.goal +
                    '</div>' +
                    '<div style="background: #ddd; border-radius: 4px; height: 8px; overflow: hidden;">' +
                    '<div style="background: ' + progress.barColor + '; height: 100%; width: ' + progress.percentage + '%; transition: width 0.3s;"></div>' +
                    '</div>' +
                    '<div style="margin-top: 8px; font-size: 12px; font-weight: bold;">' +
                    progress.percentage + '% ' + (progress.status === 'under' ? '✓ On track' : '✗ Over target') +
                    '</div>' +
                    '</div>';
                
                indicatorsContainer.innerHTML += html;
            }
        });

        if (!hasAnyGoal) {
            indicatorsContainer.innerHTML = '<p style="color: #999; grid-column: 1/-1;">No goals set yet. <a href="{{ route("goals.index") }}">Set your nutrition goals</a></p>';
        }

        //setting current time for time inputs
        var now = new Date();
        var hours = String(now.getHours()).padStart(2, '0');
        var minutes = String(now.getMinutes()).padStart(2, '0');
        var currentTime = hours + ':' + minutes;
        
        var foodTimeInput = document.getElementById('foodTime');
        var recipeTimeInput = document.getElementById('recipeTime');
        
        if (foodTimeInput) foodTimeInput.value = currentTime;
        if (recipeTimeInput) recipeTimeInput.value = currentTime;
    } catch(error) {
        console.error('Chart initialization error:', error);
    }
});
</script>
@endsection