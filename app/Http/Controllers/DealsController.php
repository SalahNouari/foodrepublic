<?php

namespace App\Http\Controllers;

use App\Areas;
use App\Deals;
use App\Item;
use App\States;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Validator;

class DealsController extends Controller
{
    
    public function add_item(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'type' => 'required',
            'name' => 'required',
            'areas_id' => 'required'
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else {
        $item = Item::find($request->item_id);
        $deal = Deals::find($request->deal_id);

        $deal->items()->attach($item, [
            'end_time' => $request->end_time, 
            'type'=> $request->type, 
            'qty' => $request->qty]);
            $response = [
                'deal' => $deal->with('items')
            ];
            return response()->json($response);
        }
    }
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'type' => 'required',
            'areas_id' => 'required'
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else {
            $deal = new Deals();
            $area = Areas::find($request->area_id);
            $deal->name = $request->name;
            $deal->end_time = $request->end_time;
            $deal->qty = $request->qty;
            $deal->type = $request->type;
            $deal->areas()->associate($area);
            $deal->save();
        }
        $response = [
            'deal' => $deal
        ];
        return response()->json($response);
    }
}
