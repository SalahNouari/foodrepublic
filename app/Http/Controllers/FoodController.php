<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vendor; 
use App\Food; 

class FoodController extends Controller
{
    public function save(Request $request)
    {
        $vendor = Vendor::where('id', $request->vendor_id)->first();
        $validator = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'required|string|max:255',
            'image' => 'required|string',
            'category' => 'required|string',
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $food = new Food;
        $food->name = $request->name;
        $food->category = $request->category;
        $food->description = $request->description;
        $food->generic = $request->generic;
        $food->image = $request->image;
        $vendor->food()->save($food);

        $response = [
            'vendor' => $vendor,
            'food' => $food,

        ];
        return response()->json($response);
    }

    public function update(Request $request)
    {
        $food = Food::find($request->id);
        $food->name = $request->name;
        $food->description = $request->description;
        $food->generic = $request->generic;
        $food->image = $request->image;
        $food->category = $request->category;
        $food->save();
        return response([
            'status' => 'success',
            'data' => $food
        ], 200);
    }
    public function find(Request $request)
    {
        $food = Food::find($request->id);
        $review_count = Reviews::where('food_id', $food->id)->count('food_id');
        $rating_avg = Reviews::where('food_id', $food->id)->avg('rating');
        return response([
            'status' => 'success',
            'data' => $food,
            'avg_rating' =>$rating_avg,
            'reviews_count' =>$review_count,
        ], 200);
    }
    public function delete(Request $request)
    {
        $food = Food::find($request->id);
        $food->delete();
   
        return response([
            'status' => 'deleted',
            'data' => $food
        ], 200);
    }
    public function all(Request $request)
    {
        $food = Food::all()->paginate(10);
   
        return response([
            'status' => 'success',
            'data' => $food
        ], 200);
    }
    public function find_by_category(Request $request)
    {
        $food = Food::where('category', $request->catgeory)
                ->where('available', 1)
                ->paginate(10);
   
        return response([
            'status' => 'success',
            'data' => $food
        ], 200);
    }
    public function add_food_to_menu(Request $request)
    {
        //Authenticate is the looged in user is a vendor
        $user_is_logged_in = Auth::user();
        $user_is_vendor = Vendor::where('verified', true)->where('user_id', $user_is_logged_in->id)->where('id', $request->vendor_id)->exists();
        if($user_is_logged_in && $user_is_vendor){
       
        $food = Food::where('vendor_id', $request->vendor_id)
                ->where('food_id',  $request->food_id)
                ->update(['available' => $request->available, 'quantity' => $request->quantity]);
   
        return response([
            'status' => 'success',
            'data' => $food
        ], 200);
    }else{
            return response([
                'status' => 'success',
                'error' => 'You have to be logged in with a vendor account to do that'
            ], 200);
    }
    }
}
