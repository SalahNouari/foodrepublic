<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Menu extends Model
{
    public function lunch()
    {
        return $this->hasMany('App\Lunch');
    }
    public function dinner()
    {
        return $this->hasMany('App\Dinner');
    }
    public function breakfast()
    {
        return $this->hasMany('App\Breakfast');
    }
    public function snacks()
    {
        return $this->hasMany('App\Snacks');
    }
    public function drinks()
    {
        return $this->hasMany('App\Drinks');
    }
}
