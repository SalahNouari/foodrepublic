<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Item extends Model
{
    public function category()
    {
        return $this->belongsTo('App\Category');
    }
    protected $hidden = ['generic', 'created_at', 'updated_at'];
    protected $withCount = ['main_option'];
    public function scopeWhereLike($query, $column, $value)
    {
        return $query->where($column, 'like', '%' . $value . '%');
    }

    public function scopeOrWhereLike($query, $column, $value)
    {
        return $query->orWhere($column, 'like', '%' . $value . '%');
    }
    public function order()
    {
        return $this->belongsToMany('App\Order')->withPivot('qty', 'tracking_id');
    }
    public function option()
    {
        return $this->belongsToMany('App\Option')->withPivot('type');
    }
    public function images()
    {
        return $this->hasMany('App\Images');
    }
    public function main_option()
    {
        return $this->belongsToMany('App\MainOption')->withPivot('type');
    }
    

}
