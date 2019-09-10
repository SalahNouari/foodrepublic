<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Food;
use App\User;
use App\Reviews;
use Illuminate\Support\Facades\Auth;
class ReviewsController extends Controller
{
    public function save(Request $request)
    {



        //get user
        $user = Auth::user();
            //check if user has reviewed product before
          $reviews =   Reviews::where('food_id', $request->food_id)
          ->where('user_id', $user->id)
          ->exists();
        if (!$reviews) {
            # code...
            $validator = $request->validate([
                'rating' => 'required|integer|max:5',
                'content' => 'required|string|max:255|',
            ]);
            if (!$validator) {
                return response(['errors' => $validator->errors()->all()], 422);
            }
      //if user has not reviewed product and review is valid
        $review = new Reviews;
        $review->content = $request->content;
        $review->rating = $request->rating;
        $review->vendor_id = $request->vendor_id;
        $review->food_id = $request->food_id;
        $user->reviews()->save($review);
        $avg_rating = Reviews::where('food_id', $request->food_id)->avg('rating');
        // update food rating and reviews count
        $food = Food::where('id', $request->food_id)->first();
        $food->increment('reviews_count', 1, ['avg_rating' => $avg_rating]);
        $review_count = $food->reviews_count;

        $response = [
            'review' => $review,
            'avg_rating' => $avg_rating,
            'review_count' => $review_count,
        ];
        return response()->json($response);
    }else{
        //if user has not reviewed poduct
            return response([
                'status' => 'failed',
                'message' => 'user already reviewed this item'
            ], 422);
    }
}
    public function update(Request $request)
    {

//get user
       
            //check if user has reviewed product before
   $reviews = Reviews::where('food_id', $request->food_id)
          ->where('user_id', $request->user_id)
          ->exists();

        if (!$reviews) {
       $user = Auth::user();
            # code...
      //if user has not reviewed product
    
        $reviews->content = $request->content;
        $reviews->rating = $request->rating;
        $reviews->vendor_id = $request->vendor_id;
        $reviews->food_id = $request->food_id;
        $user->reviews()->save($reviews);

        $avg_rating = Reviews::where('food_id', $request->food_id)->avg('rating');
        // update food rating and reviews count
        $food = Food::where('id', $request->food_id)->first();
        $food->increment('reviews_count', 1, ['avg_rating' => $avg_rating]);
        $review_count = $food->value('reviews_count');

        $response = [
            'review' => $reviews,
            'avg_rating' => $avg_rating,
            'review_count' => $review_count,
        ];
        return response()->json($response);
    }else{
        //if user has not reviewed poduct
            return response([
                'status' => 'failed',
                'message' => 'user has not reviewed before'
            ], 422);
    }
}
public function delete(Request $request){
            
    $reviews =   Reviews::where('food_id', $request->food_id)
                ->where('user_id', $request->user_id)
                ->exists();
        if (!$reviews) {
            //if user has not reviewed product
            return response([
                'status' => 'failed',
                'message' => 'you cannot delete what doesn"t exist'
            ], 422);
        }else{

            Reviews::where('user_id', $request->user_id)
            ->where('food_id', $request->food_id)
            ->delete();
            return response([
                'status' => 'success',
                'message' => 'Review has been deleted'
            ], 422);
        }
         }
 
}
