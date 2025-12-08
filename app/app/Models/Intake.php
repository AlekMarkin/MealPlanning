<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//represents a user's food or recipe consumption record
class Intake extends Model
{
    protected $fillable = [
        'user_id','intake_date','consumed_at',
        'food_id','recipe_id','quantity_g','servings',
    ];

    protected $casts = [
        'intake_date' => 'date',
        'consumed_at' => 'datetime',
    ];
}