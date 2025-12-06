@extends('layouts.app')

@section('content')
<div style="max-width: 960px; margin: 0 auto; text-align:left;">
    <h1 style="margin-bottom:10px;">Daily Intake</h1>

    @if(session('ok'))
        <div style="background:#e6ffed;border:1px solid #b7eb8f;padding:8px;margin-bottom:12px;">
            {{ session('ok') }}
        </div>
    @endif
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

    {{-- Date picker --}}
    <form method="get" action="{{ route('intakes.index') }}" style="display:flex;gap:8px;align-items:center;margin-bottom:14px;">
        <label for="date"><strong>Date:</strong></label>
        <input id="date" type="date" name="date" value="{{ $date }}" />
        <button type="submit">Go</button>
        <button type="button" onclick="window.location='{{ route('intakes.index') }}'" style="margin-left:auto;">Today</button>
    </form>

    {{-- Add Food --}}
    <fieldset style="border:1px solid #ddd;padding:10px;margin-bottom:12px;">
        <legend><strong>Add Food</strong></legend>
        <form method="post" action="{{ route('intakes.food.store') }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <select name="food_id" required style="flex:1;min-width:150px;">
                <option value="">– choose food –</option>
                @foreach($foods as $f)
                    <option value="{{ $f->id }}">{{ $f->name }}</option>
                @endforeach
            </select>
            <input type="number" name="grams" min="0" step="1" value="100" placeholder="grams (g)" style="width:80px;" />
            <input type="time" name="time" placeholder="Time" style="width:100px;" id="foodTime" />
            <button type="submit">Add</button>
            <button type="button" onclick="window.location='{{ route('foods.create') }}'" style="margin-left:auto;">+ New Food</button>
        </form>
    </fieldset>

    {{-- Add Recipe --}}
    <fieldset style="border:1px solid #ddd;padding:10px;margin-bottom:12px;">
        <legend><strong>Add Recipe</strong></legend>
        <form method="post" action="{{ route('intakes.recipe.store') }}" style="display:flex;gap:8px;align-items:center;flex-wrap:wrap;">
            @csrf
            <input type="hidden" name="date" value="{{ $date }}">
            <select name="recipe_id" required style="flex:1;min-width:150px;">
                <option value="">– choose recipe –</option>
                @foreach($recipes as $r)
                    <option value="{{ $r->id }}">{{ $r->name }}</option>
                @endforeach
            </select>
            <input type="number" name="servings" min="0" step="0.25" value="1" placeholder="servings" style="width:80px;" />
            <input type="time" name="time" placeholder="Time" style="width:100px;" id="recipeTime" />
            <button type="submit">Add</button>
            <button type="button" onclick="window.location='{{ route('recipes.create') }}'" style="margin-left:auto;">+ New Recipe</button>
        </form>
    </fieldset>

    {{-- Charts Section --}}
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 20px; margin-bottom: 20px;">
        
        {{-- Nutrients --}}
        <div style="background: white; border: 1px solid #ddd; padding: 20px; border-radius: 4px;">
            <h3 style="margin-top:0; margin-bottom:15px;">Nutrients</h3>
            <div style="position: relative; height: 300px;">
                <canvas id="goalsChart"></canvas>
            </div>
        </div>

        {{-- Carbon Footprint --}}
        <div style="background: white; border: 1px solid #ddd; padding: 20px; border-radius: 4px;">
            <h3 style="margin-top:0; margin-bottom:15px;">Carbon Footprint</h3>
            <div style="position: relative; height: 300px;">
                <canvas id="carbonChart"></canvas>
            </div>
        </div>

    </div>

    {{-- Goal Progress Indicators --}}
    <div style="background: #f9f9f9; border: 1px solid #ddd; padding: 20px; margin-bottom: 20px; border-radius: 4px;">
        <h3 style="margin-top:0; margin-bottom:15px;">Daily Goal Progress</h3>
        <div id="goalIndicators" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 15px;">
        </div>
    </div>

    {{-- Intakes table --}}
    <h3 style="margin:12px 0 6px;">Entries for {{ $date }}</h3>
    <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>Type</th>
            <th>Name</th>
            <th>Qty</th>
            <th>When</th>
            <th></th>
        </tr>
        </thead>
        <tbody>
        @forelse($intakes as $in)
            <tr>
                <td>{{ $in->food_id ? 'Food' : 'Recipe' }}</td>
                <td>
                    @if($in->food_id)
                        {{ optional(\App\Models\Food::find($in->food_id))->name ?? '–' }}
                    @elseif($in->recipe_id)
                        {{ optional(\App\Models\Recipe::find($in->recipe_id))->name ?? '–' }}
                    @else
                        –
                    @endif
                </td>
                <td>
                    @if($in->food_id)
                        {{ (int)$in->quantity_g }} g
                    @elseif($in->recipe_id)
                        {{ (float)$in->servings }} serving(s)
                    @endif
                </td>
                <td>
                    {{ $in->consumed_at ? \Illuminate\Support\Carbon::parse($in->consumed_at)->format('H:i') : '–' }}
                </td>
                <td>
                    <form method="post" action="{{ route('intakes.destroy', $in) }}" onsubmit="return confirm('Remove this entry?')" style="display:inline;">
                        @csrf @method('DELETE')
                        <button type="submit">Del</button>
                    </form>
                </td>
            </tr>
        @empty
            <tr><td colspan="5">No entries yet.</td></tr>
        @endforelse
        </tbody>
    </table>

    {{-- Totals --}}
    <h3 style="margin:14px 0 6px;">Totals</h3>
    <table border="1" cellpadding="6" cellspacing="0" width="100%">
        <thead>
        <tr>
            <th>kcal</th>
            <th>Protein</th>
            <th>Carbs</th>
            <th>Fat</th>
            <th>Fiber</th>
            <th>Sugar</th>
            <th>Sodium (mg)</th>
            <th>Carbon (gCO2e)</th>
        </tr>
        </thead>
        <tbody>
            <tr>
                <td>{{ round($totals['calories'], 2) }}</td>
                <td>{{ round($totals['protein'], 2) }}</td>
                <td>{{ round($totals['carbs'], 2) }}</td>
                <td>{{ round($totals['fat'], 2) }}</td>
                <td>{{ round($totals['fiber'], 2) }}</td>
                <td>{{ round($totals['sugar'], 2) }}</td>
                <td>{{ round($totals['sodium_mg'], 2) }}</td>
                <td>{{ round($totals['carbon_footprint_gco2e'], 2) }}</td>
            </tr>
        </tbody>
    </table>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    try {
        var totals = {
            calories: {{ $totals['calories'] ?? 0 }},
            protein: {{ $totals['protein'] ?? 0 }},
            carbs: {{ $totals['carbs'] ?? 0 }},
            fat: {{ $totals['fat'] ?? 0 }},
            fiber: {{ $totals['fiber'] ?? 0 }},
            sugar: {{ $totals['sugar'] ?? 0 }},
            sodium: {{ $totals['sodium_mg'] ?? 0 }},
            carbon: {{ $totals['carbon_footprint_gco2e'] ?? 0 }}
        };

        var goals = {
            calories: {{ $goals['calories'] ?? 'null' }},
            protein: {{ $goals['protein'] ?? 'null' }},
            carbs: {{ $goals['carbs'] ?? 'null' }},
            fat: {{ $goals['fat'] ?? 'null' }},
            fiber: {{ $goals['fiber'] ?? 'null' }},
            sugar: {{ $goals['sugar'] ?? 'null' }},
            sodium: {{ $goals['sodium'] ?? 'null' }},
            carbon: {{ $goals['carbon_footprint_gco2e'] ?? 'null' }}
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