<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public function category()
    {
        return $this->belongsTo('App\Category');
    }
    // protected $with = ['main_option'];

    public function order()
    {
        return $this->belongsToMany('App\Order')->withPivot('qty', 'tracking_id');
    }
    public function option()
    {
        return $this->belongsToMany('App\Option')->withPivot('type');
    }
    public function images()
    {
        return $this->hasMany('App\Images');
    }
    public function main_option()
    {
        return $this->belongsToMany('App\MainOption')->withPivot('type');
    }
    

}
