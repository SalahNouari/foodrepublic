<?php

namespace App;

use Laravel\Passport\HasApiTokens;
use Illuminate\Notifications\Notifiable;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Foundation\Auth\User as Authenticatable;

class User extends Authenticatable
{
    use HasApiTokens, Notifiable;

    public function food()
    {
        return $this->hasMany('App\Food');
    }
    public function address()
    {
        return $this->hasMany('App\Address');
    }
    public function reviews()
    {
        return $this->hasMany('App\Reviews');
    }
    public function vendor()
    {
        return $this->hasOne('App\Vendor');
    }
    public function delivery_agent()
    {
        return $this->hasOne('App\Delivery');
    }
    public function orders()
    {
        return $this->hasMany('App\Order');
    }
    public function area()
    {
        return $this->belongsTo('App\Areas');
    } 
    public function state()
    {
        return $this->belongsTo('App\States');
    }
    public function transactions()
    {
        return $this->hasMany('App\Transactions');
    }
    public function favourites()
    {
        return $this->hasOne('App\Favourites');
    }
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'name', 'verification_code', 'email', 'phone', 'password', 'image'
    ];

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token', 'state_id', 'area_id'
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'phone_verified_at' => 'datetime',
    ];
    public function hasVerifiedPhone()
    {
        return !is_null($this->phone_verified_at);
    }

    public function markPhoneAsVerified()
    {
        return $this->forceFill([
            'phone_verified_at' => $this->freshTimestamp(),
        ])->save();
    }
}
