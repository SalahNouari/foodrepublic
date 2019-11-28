<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Images extends Model
{
    public function items()
    {
        return $this->belongsTo('App\Item');
    }
}
