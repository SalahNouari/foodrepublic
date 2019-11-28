<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public function category()
    {
        return $this->belongsTo('App\Category');
    }
    protected $with = ['option'];
    
    public function option()
    {
        return $this->belongsToMany('App\Option')->withPivot('type');
    }
    public function images()
    {
        return $this->hasMany('App\Images');
    }

}
