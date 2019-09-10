<?php

namespace App\Http\Controllers;
use App\User;
use Illuminate\Http\Request;

class LocationController extends Controller
{
    public function save(Request $request)
    {

        $user = User::where('id', $request['id'])
            ->first();


        return $user;
    }


}
