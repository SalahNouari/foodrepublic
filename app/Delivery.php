<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Delivery extends Model
{
    public function areas()
    {
        return $this->belongsToMany('App\Areas')->withPivot('duration', 'distance');
    }

    public function user()
    {
        return $this->belongsTo('App\User');
    }

    public function vendors()
    {
        return $this->belongsToMany('App\Vendor');
    }

    public function orders()
    {
        return $this->hasMany('App\Order');
    }
}
