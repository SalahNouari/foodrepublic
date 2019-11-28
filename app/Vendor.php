<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Vendor extends Model
{
  
    public function reviews()
    {
        return $this->hasMany('App\Reviews');
    }
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function menu()
    {
        return $this->belongsTo('App\Vendor');
    }
    public function tags()
    {
        return $this->hasMany('App\Tag');
    }
    public function categories()
    {
        return $this->hasMany('App\Category');
    }
    public function option()
    {
        return $this->hasMany('App\Option');
    }
 
}
