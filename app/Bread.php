<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Bread extends Model
{
    public function breadable()
    {
        return $this->morphTo();
    }
    public function ingredients()
    {
        return $this->morphMany('App\Ingredients', 'ingredientable');
    }
}
