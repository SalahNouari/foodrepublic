<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vendor; 
use App\Menu;
use App\Reviews;
use Illuminate\Support\Facades\Auth;

use App\Http\Controllers\Controller;

class VendorController extends Controller
{
    // To get all vendors
    public function vendors()
    {

        $vendors = Vendor::all();


        $response = [
            'vendors' => $vendors,
        ];
        return response()->json($response);
    }
    // To get one vendor
    public function vendor(Request $request)
    {

        $vendor = Vendor::where('id', $request['id'])
            ->first();
        $rating_avg = Reviews::where('vedor_id', $request->id)->avg('rating');
        $response = [
            'vendor' => $vendor,
            'rating' =>$rating_avg

        ];
        return response()->json($response);
    }
    public function details(Request $request)
    {

        $vendor = Vendor::where('id', $request['id'])->first();


        $response = [
            'vendor' => $vendor,
            'address' => $vendor->address(),
            'location' => $vendor->location(),
            'specialty' => $vendor->specialty(),

        ];
        return response()->json($response);
    }
    public function popular(Request $request)
    {

        $vendor = Vendor::where('id', $request['id'])
            ->first();


        $response = [
            'popular' => $vendor->orders,
            'location' => $vendor->location,

        ];
        return response()->json($response);
    }
    public function menu(Request $request)
    {
        $menu = new Menu;
        $vendor = Vendor::where('id', $request['id'])
            ->first();


        $response = [
            'menu' => $vendor->menu,
            'location' => $vendor->location,

        ];
        return response()->json($response);
    }

    public function save(Request $request)
    {
       
        $validator = $request->validate([
            'name' => 'required|string|max:255|unique:vendors',
            'name' => 'required|string|max:255|unique:vendors',
            'user_id' => 'required|string|max:255|unique:vendors',
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        }
        $user = Auth::user();
        $user->role = $request->category;
        $user->save();

        $vendor= new Vendor;
        $vendor->name = $request->name;
        $vendor->phone = $request->phone;
        $vendor->image = $request->image;
        $vendor->bio = $request->bio;
        $user->vendor()->save($vendor);

        $response = [
            'user' => $user,
            'vendor' => $vendor,

        ];
        return response()->json($response);
        }



    public function update(Request $request)
    {
        if(Auth::user()){
        $vendor = Vendor::find($request->id);
        $vendor->name = $request->name;
        $vendor->bio = $request->bio;
        $vendor->instagram = $request->instagram;
        $vendor->facebook = $request->facebook;
        $vendor->twitter = $request->twitter;
        $vendor->image = $request->image;
        $vendor->phone = $request->phone;
        $vendor->save();
        return response([
            'status' => 'success',
            'data' => $vendor
        ], 200);
    }else{
            return response([
                'status' => 'failed',
                'data' => 'you have to be logged in to do that'
            ], 200); 
    }
    }
    public function find(Request $request)
    {
        $vendor = Vendor::find($request->user_id);

        return response([
            'status' => 'success',
            'data' => $vendor
        ], 200);
    }
    public function delete(Request $request)
    {
        $vendor = Vendor::find($request->user_id);
        $vendor->delete();

        return response([
            'status' => 'deleted',
            'data' => $vendor
        ], 200);
    }
    public function all()
    {
        $vendor = Vendor::all();

        return response([
            'status' => 'success',
            'data' => $vendor
        ], 200);
    }   
}
