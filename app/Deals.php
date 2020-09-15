<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deals extends Model
{
    public function items()
    {
        return $this->belongsToMany('App\Item');
    }

    public function vendor()
    {
        return $this->belongsTo('App\Vendor');
    }
}
