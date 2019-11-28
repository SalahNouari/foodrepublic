<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    public function vendor()
    {
        return $this->belongsTo('App\Vendor');
    }
    public function item()
    {
        return $this->belongsToMany('App\Item');
    }
}
