<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Category extends Model
{

    protected $hidden = ['created_at', 'updated_at'];

    public function items()
    {
        return $this->hasMany('App\Item');
    }
    public function vendor()
    {
        return $this->belongsTo('App\Vendor');
    }
}
