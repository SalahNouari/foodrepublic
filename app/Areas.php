<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Areas extends Model
{
    public function state()
    {
        return $this->belongsTo('App\States');
    }
    public function vendor()
    {
        return $this->belongsToMany('App\Vendor')->select('name', 'cash_on_delivery', 'lat', 'lng', 'card_on_delivery', 'vendor_id', 'image');
    }
    public function delivery_agents()
    {
        return $this->belongsToMany('App\Delivery');
    }
    public function address()
    {
        return $this->hasMany('App\Address');
    }
}
