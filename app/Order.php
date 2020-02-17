<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{

    public function user()
    {
        return $this->belongsTo('App\User');
    }
    public function address()
    {
        return $this->belongsTo('App\Address');
    }
    public function delivery()
    {
        return $this->belongsTo('App\Delivery');
    }
    public function reviews()
    {
        return $this->hasOne('App\Reviews');
    }
    public function vendor()
    {
        return $this->belongsTo('App\Vendor');
    }
    public function items()
    {
        return $this->belongsToMany('App\Item')->withPivot('qty', 'tracking_id', 'total');
    }
    public function options()
    {
        return $this->belongsToMany('App\Option')->withPivot('qty', 'type', 'tracking_id');
    }
}
