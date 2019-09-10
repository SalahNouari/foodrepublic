<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    public function reviews()
    {
        return $this->hasMany('App\Reviews');
    }
    public function food()
    {
        return $this->hasMany('App\Food');
    }
    public function user()
    {
        return $this->belongsTo('App\Reviews');
    }
    public function specialty()
    {
        return $this->hasMany('App\Specialty');
    }
    public function bread()
    {
        return $this->hasMany('App\Bread');
    }
    public function drinks()
    {
        return $this->hasMany('App\Drinks');
    }
    public function snacks()
    {
        return $this->hasMany('App\Snacks');
    }
    public function streetbite()
    {
        return $this->hasMany('App\Streetbite');
    }
}
