<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\FoodCategory;
use App\Address;
use App\Item;
use Illuminate\Support\Facades\Auth;
use Validator;

use App\Order;
use App\Location;
use App\Reply;
use App\Snacks;
use App\User;
use App\Vendor;

class ReplyController extends Controller
{

    public function all(Request $request)
    {
        $reply = Auth::user()->vendor->replys()->latest()->get();
        $response = [
            'replys' => $reply
        ];
        return response()->json($response);
    }

    public function save(Request $request)
    {
        $validator = $request->validate([
            'content' => 'required|string',
            'type' => 'required|string',
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $vendor = Auth::user()->vendor;
        $reply =  new Reply;
        $reply->content = $request->content;
        $reply->type = $request->type;
        $reply->vendor()->associate($vendor);
        $reply->save();
        $response = [
            'message' => 'reply saved successfully'
        ];
        return response()->json($response);
    }

    public function delete(Request $request)
    {
        $reply = Auth::user()->vendor->replys()->find($request->id);
        $reply->delete();
        $response = [
            'message' => 'deleted successful'
        ];
        return response()->json($response);
    }
    public function edit(Request $request)
    {
        $reply = Auth::user()->vendor->replys()->find($request->id);
        $reply->content = $request->content;
        $reply->type = $request->type;
        $reply->save();

        $response = [
            'message' => 'edit successful'
        ];
        return response()->json($response);
    }
}
