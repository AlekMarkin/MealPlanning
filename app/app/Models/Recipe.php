<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

//represents a recipe with instructions and serving information
class Recipe extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'instructions',
        'servings',
    ];

    //defines one-to-many relationship with recipe items
    public function items()
    {
        return $this->hasMany(\App\Models\RecipeItem::class);
    }
}