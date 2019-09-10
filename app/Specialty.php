<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Specialty extends Model
{
    public function specialty()
    {
        return $this->belongsTo('App\Vendor');
    }
}
