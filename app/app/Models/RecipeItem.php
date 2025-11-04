<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class RecipeItem extends Model
{
    protected $fillable = ['recipe_id','food_id','grams'];
    public $timestamps = true;

    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}
