<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Restaurant extends Model
{
    
    public function breakfast()
    {
        return $this->morphMany('App\Breakfast', 'breakfastable');
    }
    public function specialty()
    {
        return $this->morphMany('App\Specialty', 'breakfastable');
    }
    public function lunch()
    {
        return $this->morphMany('App\Lunch', 'lunchable');
    }
    public function dinner()
    {
        return $this->morphMany('App\Dinner', 'dinnable');
    }
    public function bread()
    {
        return $this->morphMany('App\Bread', 'breadable');
    }
    public function drinks()
    {
        return $this->morphMany('App\Drinks', 'drinkable');
    }
    public function cake()
    {
        return $this->morphMany('App\Cake', 'cakable');
    }
}
