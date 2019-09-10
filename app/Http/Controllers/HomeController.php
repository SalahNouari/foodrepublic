<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vendor; 
use App\Food; 
class HomeController extends Controller
{
    public function home()
    {
        $food = Food::where('available', true)
            ->latest()
            ->take(5)
            ->get();
        $chefs = Vendor::where('category', 'chef')
            ->where('approved', 'true')
            ->latest()
            ->take(5)
            ->get();
        $restaurants = Vendor::where('category', 'restaurant')
            ->where('approved', 'true')
            ->latest()
            ->take(5)
            ->get();


        $response = [
            'food' => $food,
            'chefs' => $chefs,
            'restaurants' => $restaurants,

        ];
        return response()->json($response);
    }
}
