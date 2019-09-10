<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Ingredient extends Model
{
    public function dinners()
    {
        return $this->morphedByMany('App\Dinner', 'ingredientable');
    }
}
