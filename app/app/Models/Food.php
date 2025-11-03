<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Food extends Model
{
    use HasFactory;

    // IMPORTANT: match the actual DB table name
    protected $table = 'foods';

    protected $fillable = [
        'user_id',
        'name',
        'calories',
        'protein',
        'carbs',
        'fat',
        'fiber',
        'sugar',
        'sodium_mg',
        'carbon_footprint_gco2e',
    ];
}
