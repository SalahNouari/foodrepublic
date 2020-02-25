<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MainOption extends Model
{
        public function option()
    {
        return $this->belongsToMany('App\Option');
    }
    // protected $with = ['option'];

        public function item()
    {
        return $this->belongsToMany('App\Item');
    }
 
}
