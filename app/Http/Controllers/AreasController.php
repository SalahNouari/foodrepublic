<?php

namespace App\Http\Controllers;

use App\Areas;
use App\States;
use Illuminate\Http\Request;
use Validator;


class AreasController extends Controller
{
    public function all()
    {
      $cities = States::orderBy('name')->get();
        $response = [
            'city' => $cities
        ];
        return response()->json($response);
    }
    public function cities()
    {
      $cities = States::orderBy('name')->select('name as text', 'id as value')->get();
        $response = [
            'city' => $cities
        ];
        return response()->json($response);
    }
    public function vendorarea(Request $request)
    {
      $d = States::find($request->city)->areas();
      $result = $d->get();
      $areas = $d->orderBy('name')->select('name as text', 'id as value')->get();
        $response = [
            'areas' => $areas,
            'result'=> $result
        ];
        return response()->json($response);
    }
    public function delivery(Request $request)
    {
      $d = States::find($request->city)->areas();
      $result = $d->get();
      $areas = $d->orderBy('name')->select('name as text', 'id as value')->get();
        $response = [
            'areas' => $areas,
            'result'=> $result
        ];
        return response()->json($response);
    }
    public function areas(Request $request)
    {
      $areas = States::find($request->id)->areas()->orderBy('name')
                       ->select('name as text', 'id as value')->get();
        $response = [
            'areas' => $areas
        ];
        return response()->json($response);
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
        }
        $response = [
            'areas' => $city->areas()->orderBy('name')->get()
        ];
        return response()->json($response);
    }

}
