<?php

namespace App\Http\Controllers;

use App\Areas;
use App\Item;
use App\Vendor;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
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
        $user = Auth::user();
        $user->area()->associate($d);
        $user->save();
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
        $vendor->makeHidden(['created_at', 'pos_charge', 'updated_at', 'place_id', 'account_number', 'address', 'phone', 'branch', 'type', 'account_name', 'bank_name', 'instagram', 'twitter', 'bio', 'pos_charge']);
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
                    ->select('name', 'type', 'status', 'image')
                    ->get();
        $items = array();
        foreach ($d->vendor()->get() as $vendor) {
            $d = Item::where('vendor_id', $vendor->id)
                    ->whereLike('name', $request->name)
                    ->select('name', 'available', 'id', 'image', 'price', 'vendor_name', 'category_id')
                    ->get();
                    if (count($d) > 0) {
                        Arr::add($d, 'vendor', $vendor->name);
                        Arr::add($d, 'status', $vendor->status);
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
        ->with(['tags',
        'categories' => function ($query){
            $query->withCount('items');
        }, 'area'])
        ->withCount('reviews')
        ->first();
        $t = $vendor->reviews()->avg('rating');
        data_fill($vendor, 'rate', $t);
        $vendor->makeHidden(['created_at', 'updated_at', 'place_id', 'account_number', 'phone', 'branch', 'account_name', 'bank_name', 'instagram', 'twitter']);

        $response = [
            'vendor' => $vendor,
        ];
        return response()->json($response);
    }
    public function vendoritems(Request $request)
    {
        $cat = Category::find($request->cat_id);

        $items = $cat->items()->with(['main_option'])->get();
        $items->makeHidden(['cost_price', 'mark_up_price']);
        $response = [
            'items' => $items,
        ];
        return response()->json($response);
    }
    
    public function vendoritem(Request $request)
    {
        $cat = Category::find($request->cat);
        $item = $cat->items()->where('name', $request->name)->with('main_option')->first();
        $item->makeHidden(['cost_price', 'mark_up_price']);
        $response = [
            'item' => $item,
            'vendor' => $cat->vendor->name,
            'type' => $cat->vendor->type
        ];
        return response()->json($response);
    }
    public function home(Request $request)
    {
        return 'success';

    }
    public function mainhome(Request $request)
    {
        return view('welcome');

    }
    public function loader()
    {
        return 'loaderio-9c45e264530f0331a41f35745ceb669f';

    }
 
}
