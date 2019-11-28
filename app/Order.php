<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    public function orderedFood()
    {
        return $this->hasMany('App\OrderedFood');
    }
    public function orderedOptions()
    {
        return $this->hasMany('App\OrderedOptions');
    }
    public function user()
    {
        return $this->belongsTo('App\User');
    }
}
