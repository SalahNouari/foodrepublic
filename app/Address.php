<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Address extends Model
{
    public function addressable()
    {
        return $this->morphTo();
    }
}
