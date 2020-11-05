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
    
    public function get_deals(Request $request)
    {
        $value = Cache::tags(['deals', 'area_'.$request->area_id])->rememberForever('deals_area_'.$request->area_id.'_'.$request->type, function () use ($request) {

        $validator = Validator::make($request->all(), [
            'area_id' => 'required',
            'type' => 'required'
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else {
        $area = Areas::find($request->area_id);
        $deal = $area->deals()->where('type', $request->type)
        ->with(['items'=> function ($query){
            $query->select('name', 'image', 'vendor_name', 'item_id', 'category_id', 'price');
        },])->get();

            $response = [
                'deal' => $deal
            ];
            return $response;
        }
    });
        return response()->json($value);
    
        }
    
    public function remove_item(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'deal_id' => 'required',
            'item_id' => 'required',
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else {
        $deal = Deals::find($request->deal_id);

        $deal->items()->detach($request->item_id);
        $dealR = $deal->with(['items'=> function ($query){
            $query->select('name', 'image', 'vendor_name', 'item_id', 'category_id', 'price');
        },])->get();
        Cache::tags(['deals', 'area_'.$request->area_id])->flush();
            $response = [
                'deal' => $dealR
            ];
            return response()->json($response);
        }
    }
    public function add_item(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'deal_id' => 'required',
            'item_id' => 'required',
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else {
        $item = Item::find($request->item_id);
        $deal = Deals::find($request->deal_id);
        Cache::tags(['deals', 'area_'.$request->area_id])->flush();

        $deal->items()->attach($item, [
            'end_time' => $request->end_time, 
            'type'=> $request->type, 
            'status'=> $request->status, 
            'qty' => $request->qty]);
            $response = [
                'deal' => $deal->with('items')->get()
            ];
            return response()->json($response);
        }
    }
    public function remove_deal(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required',
            'areas_id' => 'required'
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else {
        $area = Areas::find($request->area_id);
            $deal = Deals::find($request->id);
            $deal->area()->dissociate($area);
            $deal->save(); 
            $response = [
                'deal' => $deal
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
            $deal->type = $request->type;
            $deal->area()->associate($area);
            $deal->save();
        }
        $response = [
            'deal' => $deal
        ];
        return response()->json($response);
    }

}
