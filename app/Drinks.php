<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Drinks extends Model
{
    public function drinkable()
    {
        return $this->morphTo();
    }
}
