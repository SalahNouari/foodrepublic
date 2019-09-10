<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Snacks extends Model
{
    public function ingredients()
    {
        return $this->morphMany('App\Ingredients', 'ingredientable');
    }
}
