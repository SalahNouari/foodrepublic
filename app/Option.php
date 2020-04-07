<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Option extends Model
{
    public function vendor()
    {
        return $this->belongsTo('App\Vendor');
    }
    protected $hidden = ['created_at', 'updated_at'];

    public function item()
    {
        return $this->belongsToMany('App\Item');
    }
    public function main_option()
    {
        return $this->belongsToMany('App\MainOption');
    }
    public function order()
    {
        return $this->belongsToMany('App\Order')->withPivot('type', 'qty', 'tracking_id');
    }
}
