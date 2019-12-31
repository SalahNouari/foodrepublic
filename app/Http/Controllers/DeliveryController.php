<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vendor;
use App\Menu;
use App\Delivery;
use App\Reviews;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Support\Facades\Auth;
use AfricasTalking\SDK\AfricasTalking;
use App\Areas;
use App\Http\Controllers\Controller;
use App\States;

class DeliveryController extends Controller
{

    public function allvendors(Request $request)
    {
        $d = States::find($request->id)->areas()->with('vendor')->latest()->get();
        // $result = $d->get();
        $vendors = array();
        
        foreach ($d as $item) {
            foreach ($item['vendor'] as  $value) {
                # code...
                $vendors[] = $value;
            }
        }
        $response = [
            'vendors' => $vendors,
            // 'result' => $result
        ];
        return response()->json($response);
    }
    public function agents(Request $request)
    {
        $agents = Auth::user()->vendor->delivery_agents()->latest()->get();
        $response = [
            'agents' => $agents
        ];
        return response()->json($response);
    }


    public function load(Request $request)
    {
        $delivery_agent = Auth::user()->delivery_agent()->with(['vendors',  'areas'])->withCount(['orders' => function ($query) {
            $query->where('status', 4);
        }])->get();
        // $rating_avg = Reviews::where('vendor_id', $request->id)->avg('rating');
        $response = [
            'delivery_agent' => $delivery_agent,
            // 'rating' => $rating_avg
        ];
        return response()->json($response);
    }


    public function upload(Request $request)
    {
        $files = $request->file('files');
        request()->validate([
            'files' => 'required',
            'files.*' => 'image|mimes:jpeg,JPG,png,jpg,gif,svg|max:2048'
        ]);
        $delivery_agent = Auth::user()->delivery_agent;
        foreach ($files as $file) {
            $image_name = $file->getRealPath();
            Cloudder::upload($image_name, null, array("width" => 400, "height" => 400, "crop" => "fit", "quality" => "auto", "fetch_format" => "auto"));
            $image_url = Cloudder::show(Cloudder::getPublicId(), ["width" => 400, "height" => 400]);
            $delivery_agent->image = $image_url;
        }
        $delivery_agent->save();
        $success['message'] = 'Image uploaded successfully';
        return response()->json(['success' => $success], 200);
    }
    public function find(Request $request)
    {
        $delivery_agent = Delivery::where('id', $request->id)
            ->first();
        $response = [
            'vendor' => $delivery_agent,
        ];
        return response()->json($response);
    }

    public function paySet(Request $request)
    {
        $user = Auth::user();
        $delivery_agent = $user->delivery_agent;
        $delivery_agent->account_name = $request->account_name;
        $delivery_agent->account_number = $request->account_number;
        $delivery_agent->bank_name = $request->bank_name;
        $delivery_agent->save();
        $response = [
            'message' => 'successful'
        ];
        return response()->json($response);
    }

    public function save(Request $request)
    {

        $validator = $request->validate([
            'name' => 'required|string|max:255|unique:deliveries',
            'phone' => 'required|string|max:255|unique:deliveries',
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $user = Auth::user();
        $user->role = $request->category;
        $user->save();
        $delivery_agent = new Delivery;
        $delivery_agent->name =  $request->name;
        $delivery_agent->phone = $request->phone;
        $delivery_agent->bio = $request->bio;
        $delivery_agent->address = $request->address;
        $delivery_agent->city = $request->city;
        $delivery_agent->lat = $request->lat;
        $delivery_agent->lng = $request->lng;
        $vendors = $request->vendor;
        $delivery_agent->place_id = $request->place_id;

        $delivery_agent->vendors()->sync($vendors);
        $delivery_agent->user()->associate($user);
        $delivery_agent->save();
        $response = [
            'delivery_agent' => $delivery_agent
        ];
        return response()->json($response);
    }

    public function update(Request $request)
    {

        $delivery_agent = Auth::user()->delivery_agent;
        $delivery_agent->name = $request->name;
        $delivery_agent->bio = $request->bio;
        $delivery_agent->phone = $request->phone;
        $delivery_agent->save();
        $areas = $request->areas;
        $vendors = $request->vendors;
        $delivery_agent->vendors()->sync($vendors);
        $delivery_agent->areas()->sync($areas);
        $duration = $request->duration;
        $distance = $request->distance;

        foreach ($areas as $i => $area) {
            $delivery_agent->areas()->updateExistingPivot($area, ['distance' => $distance[$i], 'duration' => $duration[$i]]);
        }
        return response([
            'status' => 'success',
        ], 200);
    }

    public function delete(Request $request)
    {
        if (Auth::user()->role === 'admin') {
            $delivery_agent = Delivery::find($request->delivery_agent_id);
            $delivery_agent->delete();
        }
        return response([
            'status' => 'deleted',
            'data' => $delivery_agent
        ], 200);
    }

}