<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
    public function area()
    {
        return $this->belongsToMany('App\Areas')->withPivot('fee', 'duration', 'distance');
    }
    public function reviews()
    {
        return $this->hasMany('App\Reviews');
    }
    public function scopeWithAvgRating($query)
    {
        return $query->hasMany('App\Reviews')->avg('rating');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function menu()
    {
        return $this->belongsTo('App\Vendor');
    }
    public function favourites()
    {
        return $this->belongsToMany('App\Favourites');
    }
    public function tags()
    {
        return $this->belongsToMany('App\Tag');
    }
    public function categories()
    {
        return $this->hasMany('App\Category');
    }
    public function main_option()
    {
        return $this->hasMany('App\MainOption');
    }
    public function orders()
    {
        return $this->hasMany('App\Order');
    }
    public function delivery_agents()
    {
        return $this->belongsToMany('App\Delivery');
    }
    public function replys()
    {
        return $this->hasMany('App\Reply');
    }
    public function option()
    {
        return $this->hasMany('App\Option');
    }
 
}
