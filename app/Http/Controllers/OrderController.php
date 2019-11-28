<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;  
use Illuminate\Support\Facades\Response;
use App\FoodCategory; 
use App\Address;
use App\Order; 
use App\Location; 
use App\Snacks; 
use App\User; 


class OrderController extends Controller
{
//     var myarray = [];

// myarray.push({
//     "Name": 'Adam',
//     "Age": 33
// });

// myarray.push({
//     "Name": 'Emily',
//     "Age": 32
// });

// myarray[0]["Address"] = "123 Some St.";

// console.log( JSON.stringify( myarray, null, 2 ) );
// myarray[0].address = { presentAddress: "my present address..." };
// and can get the value as: myarray[0].address.presentAddress;
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
  public function save_order(Request $request)
{
  
    $orders = $request->json()->all(); 
    foreach ($orders as $order) {
        Order::create($order);
    }
}
}

