<?php

namespace App\Http\Controllers;

use App\Address;
use App\Areas;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;


class AddressController extends Controller
{
    public function find(Request $request)
    {
        $address = Auth::user()->address()->with('area')->find($request->id);
        $response = [
            'address' => $address
        ];
        return response()->json($response);
    }
    public function all_address(Request $request)
    {
        $address = Auth::user()->address()->select('name', 'user_id', 'id')->get();
        $response = [
            'address' => $address
        ];
        return response()->json($response);
    }
    public function all(Request $request)
    {
        $address = Auth::user()->address()->with('area')->where('area_id', $request->area_id)->select('area_id', 'instruction', 'name', 'created_at', 'user_id', 'id')->get();
        $response = [
            'address' => $address
        ];
        return response()->json($response);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'area_id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        } else {
            $user = Auth::user();
            $area = Areas::find($request->area_id);
            $address = new Address;
            $address->name = $request->name;
            $address->company = $request->company;
            $address->name_2 = $request->name_2;
            $address->instruction = $request->instruction;
            $address->lat = $request->lat;
            $address->lng = $request->lng;
            $address->place_id = $request->place_id;
            $address->user()->associate($user);
            $address->area()->associate($area);
            $address->save();
            $success['address'] = $address;
            return response()->json(['success' => $success], 200);
        }
    }

    public function edit(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        } else {
            $address = Auth::user()->address()->find($request->id);
            $address->name = $request->name;
            $address->company = $request->company;
            $address->name_2 = $request->name_2;
            $address->instruction = $request->instruction;
            $address->lat = $request->lat;
            $address->lng = $request->lng;
            $address->place_id = $request->place_id;
           
            $address->save();
            $success['address'] = $address;
            return response()->json(['success' => $success], 200);
        }
    }

    public function delete(Request $request)
    {
        $user = Auth::user();
        $address = $user->address->find($request->id);
        $address->delete();
        $response = [
            'message' => 'Address has been deleted',
        ];
        return response()->json($response);
    }
}
