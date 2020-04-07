<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class MainOption extends Model
{
        public function option()
    {
        return $this->belongsToMany('App\Option');
    }
    protected $hidden = ['created_at', 'updated_at'];

    protected $with = ['option'];

        public function item()
    {
        return $this->belongsToMany('App\Item');
    }
 
}
