<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tag extends Model
{
    protected $hidden = ['created_at', 'updated_at'];
    public function vendor()
    {
        return $this->belongsToMany('App\Vendor');
    }
}
