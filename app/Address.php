<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    public function user()
    {
        return $this->belongsTo('App\User');
    }
    protected $with = ['area'];

    public function order()
    {
        return $this->hasMany('App\Order');
    }
    public function area()
    {
        return $this->belongsTo('App\Areas');
    }
}
