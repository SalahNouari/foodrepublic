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
        $vendor = $d->vendor()
        ->select('name', 'cash_on_delivery', 'lat', 'lng', 'card_on_delivery', 'id', 'image')
        ->with( [
        'tags',
         'area' => function ($query) use ($request) {
            $query->where('areas_id', $request->id);
        }])
        ->select('name', 'cash_on_delivery', 'lat', 'lng', 'card_on_delivery', 'id', 'image')
        ->get();
        $response = [
            'items' => $vendor
        ];
        return response()->json($response);
    }
    public function search(Request $request)
    {

        $d = Areas::find($request->id);
        
        $vendors = $d->vendor()
                    ->where('name', 'like', '%' . $request->name . '%')
                    ->select('name', 'image')
                    ->get();
        $items = array();
        foreach ($d->vendor()->get() as $vendor) {
            $d = Item::where('vendor_id', $vendor->id)
                    ->whereLike('name', $request->name)
                    ->select('name', 'available', 'id', 'image', 'price', 'vendor_name', 'category_id')
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
        ->with('items')->get();
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
