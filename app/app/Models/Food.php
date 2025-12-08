<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

//represents a food item with nutritional data
class Food extends Model
{
    use HasFactory;

    //specifies the database table name
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