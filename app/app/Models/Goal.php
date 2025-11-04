<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Goal extends Model
{
    protected $fillable = [
        'user_id', 'metric', 'target_value', 'period',
    ];

    public static function metrics(): array
    {
        return [
            'calories' => ['label' => 'Calories', 'unit' => 'kcal'],
            'protein' => ['label' => 'Protein', 'unit' => 'g'],
            'carbs' => ['label' => 'Carbs', 'unit' => 'g'],
            'fat' => ['label' => 'Fat', 'unit' => 'g'],
            'fiber' => ['label' => 'Fiber', 'unit' => 'g'],
            'sugar' => ['label' => 'Sugar', 'unit' => 'g'],
            'sodium' => ['label' => 'Sodium', 'unit' => 'mg'],
            'carbon_footprint_gco2e' => ['label' => 'Carbon Footprint', 'unit' => 'gCO2e'],
        ];
    }

    public static function comparators(): array
    {
        return [
            'at_most' => ['label' => 'At most (≤)', 'symbol' => '≤'],
            'at_least' => ['label' => 'At least (≥)', 'symbol' => '≥'],
        ];
    }
}
