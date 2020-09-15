<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Deals extends Model
{
    public function items()
    {
        return $this->belongsToMany('App\Item')->withPivot('qty', 'type', 'status', 'end_time');
    }

    public function area()
    {
        return $this->belongsTo('App\Areas');
    }
}
