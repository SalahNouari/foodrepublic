<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Areas extends Model
{
    protected $hidden = ['created_at', 'updated_at'];

    public function state()
    {
        return $this->belongsTo('App\States');
    }
    public function vendor()
    {
        return $this->belongsToMany('App\Vendor');
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
