<?php

namespace App\Http\Controllers;

use App\Areas;
use App\States;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Validator;


class AreasController extends Controller
{
    public function all()
    {
      $value = Cache::rememberForever('cities', function () {

      $cities = States::orderBy('name')->get();
       return $response = [
            'city' => $cities
        ];
        // 1. store key value objcts of vendors and their properties
        // then return the vendors individually by running a paginated requests to they individual vendors 
        // just like a channel
        // 2. embedding vendor information on qrcode storing the pictures in low quality and the deciphering the qrcode in the app, such that the images are loaded from cache
    });
    return response()->json($value);
    }
    public function cities()
    {
    $value = Cache::rememberForever('cities_key', function () {
      $cities = States::orderBy('name')->select('name as text', 'id as value')->get();
        return $response = [
            'city' => $cities
        ];
    });
        return response()->json($value);
    }
    public function vendorarea(Request $request)
    {
        $d = States::find($request->city)->areas();
        $value = Cache::rememberForever('vendor_area_'.$request->id, function () use ($d) {

      $result = $d->get();
      $areas = $d->orderBy('name')->select('name as text', 'id as value')->get();
        return $response = [
            'areas' => $areas,
            'result'=> $result
        ];
    });
        return response()->json($value);
    }
    public function delivery(Request $request)
    {
        $d = States::find($request->city)->areas();
        $value = Cache::rememberForever('delivery_areas_'.$request->city, function () use ($d) {

      $result = $d->get();
      $areas = $d->orderBy('name')->select('name as text', 'id as value')->get();
       return $response = [
            'areas' => $areas,
            'result'=> $result
        ];
    });
        return response()->json($value);
    }
    public function areas(Request $request)
    {
        $state = States::find($request->id);
        $user = Auth::user();
        $user->state()->associate($state);
        $user->save();
        $value = Cache::rememberForever('areas_'.$request->id, function () use ($state){

        $areas = $state->areas()->orderBy('name')
                       ->select('name as text', 'id as value', 'lat', 'lng')->get();
        return $response = [
            'areas' => $areas
        ];
    });
        return response()->json($value);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'lat' => 'required',
            'lng' => 'required',
            'place_id' => 'required'
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else {
            $city = new States;
            $city->name = $request->name;
            $city->place_id = $request->place_id;
            $city->lng = $request->lng;
            $city->lat = $request->lat;
            $city->save();
            Cache::forget('cities_key');
            Cache::forget('cities');

        }
        $response = [
            'city' => $city
        ];
        return response()->json($response);
    }
    public function savearea(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'lat' => 'required',
            'lng' => 'required',
            'city_id' => 'required',
            'place_id' => 'required'
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else {
            $city = States::find($request->city_id);
            $area = new Areas;
            $area->name = $request->name;
            $area->place_id = $request->place_id;
            $area->lng = $request->lng;
            $area->lat = $request->lat;
            $city->areas()->save($area);
            Cache::forget('delivery_areas_'.$request->city_id);
            Cache::forget('vendor_area_'.$request->city_id);
            Cache::forget('areas_'.$request->city_id);
        }
        $response = [
            'areas' => $city->areas()->orderBy('name')->get()
        ];
        return response()->json($response);
    }

}
