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
use App\Order; 
use App\Location; 
use App\Ingredient; 
use App\Snacks; 
use App\User; 


class OrderController extends Controller
{

    // To get all categpries
    public function paid()
    {
       
        $pics = FoodCategory::latest()
                      ->select('created_at', 'images', 'category', 'photographername', 'title', 'id', 'tags')
                      ->paginate(4);
                     
        $latestpics = Gallery::latest()
                      ->orderBy('views', 'desc')
                      ->select('created_at', 'images', 'category', 'photographername', 'title', 'id', 'tags')
                      ->take(3)
                      ->get();
     $response = [
            'pics' => $pics,
            'popularpics' =>$latestpics
        ];
      return response()->json($response);
  
  }
}

