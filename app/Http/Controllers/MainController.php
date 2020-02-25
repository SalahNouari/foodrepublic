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
        ->where('type', $request->type)
        ->with([
             'tags' => function ($query){
                $query->select('tag');
            },
             'area' => function ($query) use ($request) {
                $query->where('areas_id', $request->id);
            }
            ])
        ->withCount('reviews')
        ->get();
        $vendor->each(function ($i, $k){
            $t = $i->reviews()->avg('rating');
                data_fill($i, 'rate', $t);

        });
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
        $vendor = Vendor::where('name', $request->name)
        ->with(['tags','categories', 'area'])
        ->withCount('reviews')
        ->first();
        $t = $vendor->reviews()->avg('rating');
        data_fill($vendor, 'rate', $t);

        $response = [
            'vendor' => $vendor,
        ];
        return response()->json($response);
    }
    public function vendoritems(Request $request)
    {
        $items = Vendor::where('name', $request->name)->first()->categories()
        ->where('id', $request->cat_id)
        ->with(['items.main_option' => function($query){
            $query->select('id', 'vendor_id', 'title');
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
