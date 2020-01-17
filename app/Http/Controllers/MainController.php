<?php

namespace App\Http\Controllers;

use App\Areas;
use App\Item;
use App\Vendor;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;  
use Illuminate\Support\Facades\Response;


class MainController extends Controller
{
    public function settings(Request $request)
    {

        $setting = DB::select('select * from settings where type = :name', ['name' => $request->name]);
        $response = [
            'settings' => $setting
        ];
        return response()->json($response);
    }
    public function page(Request $request)
    {

        $d = Areas::find($request->id);
        $vendor = $d->vendor()->with( ['tags', 'area' => function ($query) use ($request) {
            $query->where('areas_id', $request->id);
        }])->get();
        $response = [
            'items' => $vendor
        ];
        return response()->json($response);
    }
    public function search(Request $request)
    {

        $d = Areas::find($request->id);
        $vendors = $d->vendor()->whereLike('name', $request->name)->get();
        $items = array();
        foreach ($d->vendor()->get() as $key => $vendor) {
            $d = Item::where('vendor_id', $vendor->id)
                    ->whereLike('name', $request->name)
                    ->get();
                    if (count($d) > 0) {
                        array_push($items, $d);
                    }
        }
        
        $response = [
            'vendors' => $vendors,
            'items' => $items
        ];
        return response()->json($response);
    }
    public function vendorpage(Request $request)
    {
        $vendor = Vendor::where('name', $request->name)->with(['tags','categories', 'area'])->first();
        $response = [
            'vendor' => $vendor,
        ];
        return response()->json($response);
    }
    public function vendoritems(Request $request)
    {
        $items = Vendor::where('name', $request->name)->first()->categories()
        ->where('id', $request->cat_id)
        ->with(['items' => function ($query) {
    $query->where('available', true);
}])->get();
        $response = [
            'items' => $items,
        ];
        return response()->json($response);
    }
    public function vendoritem(Request $request)
    {
        $item = Category::find($request->cat)->items()->where('name', $request->name)->with('main_option')->first();
        $response = [
            'item' => $item,
        ];
        return response()->json($response);
    }
 
}
