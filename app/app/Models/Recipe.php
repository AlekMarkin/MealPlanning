<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Recipe extends Model
{
    protected $fillable = [
        'user_id',
        'name',
        'instructions',
    ];

    public function items()
    {
        return $this->hasMany(\App\Models\RecipeItem::class);
    }
}
