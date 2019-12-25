<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vendor;
use App\Menu;
use App\Tag;
use App\Reviews;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Support\Facades\Auth;
use AfricasTalking\SDK\AfricasTalking;

use App\Http\Controllers\Controller;

class VendorController extends Controller
{
    // To get all vendors
    public function vendors()
    {
        $vendors = Vendor::where('verified', 1)->paginate(10);
     
        $response = [
            'vendors' => $vendors,
        ];
        return response()->json($response);
    }
    public function load(Request $request)
    {
        $vendor = Auth::user()->vendor()->with(['tags',  'area'])->withCount(['orders' => function ($query) {
                    $query->where('paid', false);
            }])->get();
        // $rating_avg = Reviews::where('vendor_id', $request->id)->avg('rating');
        $response = [
            'vendor' => $vendor,
            // 'rating' => $rating_avg
        ];
        return response()->json($response);
    }



    public function tags(Request $request)
    {
        $tags = Tag::select('tag as text', 'id as value')->get();
        // $rating_avg = Reviews::where('vendor_id', $request->id)->avg('rating');
        $response = [
            'tags' => $tags,
            // 'rating' => $rating_avg
        ];
        return response()->json($response);
    }
    public function upload(Request $request)
    {
        $files = $request->file('files');
        request()->validate([
            'files' => 'required',
            'files.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ]);
        $vendor = Auth::user()->vendor;
        foreach ($files as $file) {
            $image_name = $file->getRealPath();
            Cloudder::upload($image_name, null, array("width" => 400, "height" => 400, "crop" => "fit", "quality" => "auto", "fetch_format" => "auto"));
            $image_url = Cloudder::show(Cloudder::getPublicId(), ["width" => 400, "height" => 400]);
            $vendor->image = $image_url;
            
        }
        $vendor->save();
        $success['message'] = 'Image uploaded successfully';
        return response()->json(['success' => $success], 200);
    }
    public function find(Request $request)
    {
        $vendor = Vendor::where('user_id', $request->user_id)
            ->first();
        // $rating_avg = Reviews::where('vendor_id', $request->id)->avg('rating');
        $response = [
            'vendor' => $vendor,
            // 'rating' => $rating_avg
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
            'specialty' => $vendor->specialty()
        ];
        return response()->json($response);
    }
    public function popular(Request $request)
    {

        $vendor = Vendor::where('id', $request['id'])
            ->first();
        $response = [
            'popular' => $vendor->orders,
            'location' => $vendor->location
        ];
        return response()->json($response);
    }
    public function setfee(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $areas = $vendor->area;
        $fee = $request->fee;
        
        foreach ($areas as $i=>$area) {
            $vendor->area()->updateExistingPivot($area->id, ['fee' => $fee[$i]]);
        }
            
        $response = [
                'message' => 'successful'
            ];
        return response()->json($response);
    }
    public function paySet(Request $request)
    {
        $user = Auth::user();
        $vendor = $user->vendor;
        $vendor->cash_on_delivery = $request->cash;
        $vendor->card_on_delivery = $request->card;
        $vendor->minimum_order = $request->minimum;
        $vendor->account_name = $request->account_name;
        $vendor->account_number = $request->account_number;
        $vendor->bank_name = $request->bank_name;
        $vendor->save();
        $response = [
                'message' => 'successful'
            ];
        return response()->json($response);
    }
    public function menu(Request $request)
    {
        $vendor = Vendor::find($request['id']);
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
            'user_id' => 'required'
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $user = Auth::user();
        $user->role = $request->category;
        $user->save();
        $vendor = new Vendor;
        $vendor->name =  $request->name;
        $vendor->phone = $request->phone;
        $vendor->bio = $request->bio;
        $vendor->address = $request->address;
        $vendor->city = $request->city;
        $vendor->lat = $request->lat;
        $vendor->lng = $request->lng;
        $vendor->place_id = $request->place_id;

        $tags = $request->tags;
        $areas = $request->areas;

        $user->vendor()->save($vendor);

        $vendor->area()->attach($areas);
        $vendor->tags()->attach($tags);

        $response = [
            'message' => 'Registeration successful'
        ];
        return response()->json($response);
    }

    public function update(Request $request)
    {
      
            $vendor = Auth::user()->vendor;
            $vendor->name = $request->name;
            $vendor->bio = $request->bio;
            $vendor->phone = $request->phone;
            $vendor->save();
            $tags = $request->tags;
            $areas = $request->areas;
            $vendor->area()->sync($areas);
            $vendor->tags()->sync($tags);
            $duration = $request->duration;
            $distance = $request->distance;

            foreach ($areas as $i => $area) {
                $vendor->area()->updateExistingPivot($area, ['distance' => $distance[$i], 'duration' => $duration[$i]]);
            }
            return response([
                'status' => 'success',
            ], 200); 
    }

    public function delete(Request $request)
    {
        if (Auth::user()->role === 'admin') {
            $vendor = Vendor::find($request->vendor_id);
            $vendor->delete();
        }
        return response([
            'status' => 'deleted',
            'data' => $vendor
        ], 200);
    }
    public function all()
    {
        $vendor = Auth::user()->vendor->categories;
        return response([
            'status' => 'success',
            'data' => $vendor
        ], 200);
    }
}
