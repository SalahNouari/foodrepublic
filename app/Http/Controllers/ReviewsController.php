<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Food;
use App\Order;
use App\User;
use App\Reviews;
use App\Vendor;
use Illuminate\Support\Facades\Auth;
class ReviewsController extends Controller
{
    public function save(Request $request)
    {
        //get user
        $user = Auth::user();
 
            # code...
            $validator = $request->validate([
                'rating' => 'required|integer|max:5',
                'review' => 'string|max:100|',
                'order_id' => 'required|integer',
                'vendor_id' => 'required|integer',
            ]);
            if (!$validator) {
                return response(['errors' => $validator->errors()->all()], 422);
            } else{
        
      //if user has not reviewed product and review is valid
        $review = new Reviews;
        $order = Order::find($request->order_id);
        $vendor = Vendor::find($request->vendor_id);
        $review->content = $request->review;
        $review->rating = $request->rating;
        $review->vendor_id = $request->vendor_id;
        $user->reviews()->associate($review);
        $vendor->reviews()->associate($review);
        $order->reviews()->save($review);
        $review->save();
     
        $response = [
            'message' => 'review saved',
        ];
        return response()->json($response);
        }
}
    public function update(Request $request)
    {
        $validator = $request->validate([
            'rating' => 'required|integer|max:5',
            'review' => 'required|string|max:255|'
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else {

        //get user
        $user = Auth::user();
        $review = $user->reviews->find($request->review_id);
        $review->content = $request->review;
        $review->rating = $request->rating;
        $review->save();

        $response = [
            'message' => 'updated successfully'
        ];
        return response()->json($response);
        }
}
public function delete(Request $request){
            
     Reviews::where('id', $request->id)
                ->delete();
         }
 
}
