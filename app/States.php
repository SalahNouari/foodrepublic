<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class States extends Model
{
    public function vendor()
    {
        return $this->hasMany('App\Vendor');
    }
    public function areas()
    {
        return $this->hasMany('App\Areas');
    }
}
