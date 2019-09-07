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
    // To get all chefs
    public function chefs()
    {
       
        $chefs = Chef::all();
                     
       
     $response = [
            'chefs' => $chefs,
        ];
      return response()->json($response);
  
  }
    // To get one chef
    public function chef(Request $request)
    {
       
        $chef = Chef::where('id', $request['id'] )
                      ->first();
                     
       
     $response = [
            'chef' => $chef,
        ];
      return $chef;
  
  }
}
