<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
class Reviews extends Model
{
    use HasApiTokens, Notifiable;

    public function user()
    {
        return $this->belongsTo('App\Reviews');
    }

    public function vendor()
    {
        return $this->belongsTo('App\Vendor');
    }
}
