<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Cake extends Model
{
    public function cakable()
    {
        return $this->morphTo();
    }
    public function ingredients()
    {
        return $this->morphMany('App\Ingredients', 'ingredientable');
    }
}
