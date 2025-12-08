<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//represents an ingredient in a recipe with quantity in grams
class RecipeItem extends Model
{
    protected $fillable = ['recipe_id','food_id','grams'];
    public $timestamps = true;

    //defines inverse relationship to parent recipe
    public function recipe()
    {
        return $this->belongsTo(Recipe::class);
    }

    //defines relationship to the food item
    public function food()
    {
        return $this->belongsTo(Food::class);
    }
}