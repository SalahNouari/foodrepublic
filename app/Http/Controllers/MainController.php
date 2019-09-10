<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;  
use Illuminate\Support\Facades\Response;
use App\FoodCategory; 
use App\Restaurant; 
use App\Chef; 
use App\Bread; 
use App\Cake; 
use App\Breakfast; 
use App\Drinks; 
use App\Dinner; 
use App\Lunch; 
use App\Menu; 
use App\Ingredient; 
use App\Snacks; 
use App\User; 


class MainController extends Controller
{

    // To get one chef
    public function chef(Request $request)
    {
       
        $chef = Chef::where('id', $request['id'] )
                      ->first();
       $user= User::where('id', $request['user_id']);
       $user->address();
       
     $response = [
            'chef' => $chef,
            'address' => $chef->address,
            'location' => $chef->location,
            'reviews' => $chef->reviews,
            'sales' => $chef->orders,

        ];
      return $chef;
  
  }
  
}
