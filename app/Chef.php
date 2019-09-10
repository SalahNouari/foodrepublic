<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Chef extends Model
{
    public function reviews()
    {
        return $this->hasMany('App\Reviews');
    }
    public function user()
    {
        return $this->belongsTo('App\Reviews');
    }
    public function bread()
    {
        return $this->morphMany('App\Bread', 'breadable');
    }
    public function drinks()
    {
        return $this->morphMany('App\Drinks', 'drinkable');
    }
    public function snacks()
    {
        return $this->morphMany('App\Snacks', 'snackable');
    }
    public function streetbite()
    {
        return $this->morphMany('App\Streetbite', 'streetable');
    }
}
