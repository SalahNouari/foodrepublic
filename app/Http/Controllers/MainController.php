<?php

namespace App\Http\Controllers;

use App\Areas;
use App\Item;
use App\Vendor;
use App\Category;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
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
    public function remove_poll(Request $request)
    {
        Cache::tags(['poll', 'vendors'.$request->area_id])->flush();

    }
    public function add_poll(Request $request)
    {

    }
    public function poll(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'poll' => 'required|string',
            ]);
            if (!$validator) {
                return response(['errors' => $validator->errors()->all()], 422);
            } else {
                $polls = json_decode($request->poll);
                $vendors = DB::table('vendors')
                ->whereIn('id', $polls);
                $vendors->update([
                    'votes'=> DB::raw('votes+1'),
                    'updated_at' => Carbon::now()
                    ]);
        $response = [
            'poll' => $vendors->get()
    ];
        return response()->json($response);
            }
    }
    public function policy(Request $request)
    {

        $policy = DB::select('select * from settings where type = :name', ['name' => $request->name]);
        $response = [
            'policy' => $policy
        ];
        return response()->json($response);
    }
    public function flushCache(Request $request){
        Cache::flush();
        $response = [
            'msg' => 'success'
    ];
        return response()->json($response);
    }
    public function page(Request $request)
    {
        $d = Areas::find($request->id);
        $city = $d->states_id;
        if(Auth::guard('api')->check()){
            $user = Auth::guard('api')->user();
            $user->area()->associate($d);
            $user->save();
        }
        Cache::tags(['pages_'.$city])->flush();
        $value = Cache::tags(['pages_'.$city, $request->type])->remember('page_'.$request->id.'_'.$request->type, Carbon::now()->addMinutes(60 * 24), function () use ($request, $d) {
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
                ->orderBy('verified', 'desc')
                ->get();
                $deal = $d->deals()->where('type', $request->type)->
                select('id', 'name')
                ->with(['items'=> function ($query){
                    $query->select('name', 'image', 'vendor_name', 'item_id', 'category_id', 'price')
                    ->withCount('main_option');
                },])->get();
                
           
        $vendor->makeHidden(['created_at', 'token', 'verified', 'votes', 'user_id','city', 'pos_charge', 'updated_at', 'place_id', 'account_number', 'address', 'phone', 'branch', 'type', 'account_name', 'bank_name', 'instagram', 'twitter', 'bio', 'pos_charge']);
        $vendor->each(function ($i, $k){
            $t = $i->reviews()->avg('rating');
                data_fill($i, 'rate', $t);

        });
        return  $response = [
            'items' => $vendor,
            'deals' => $deal
                ];
    });
        return response()->json($value);
    }

    //eliminate the foreach
    public function search2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'type' => 'required|string',
            ]);
            if (!$validator) {
                return response(['errors' => $validator->errors()->all()], 422);
            } else {
        $d = Areas::find($request->id);
        
        $vendors = $d->vendor()
                    ->where('type', $request->type)
                    ->where('name', 'like', '%' . $request->name . '%')
                    ->select('name', 'type', 'status', 'image')
                    ->distinct()
                    ->get();
                    $items = array();
                    $vend = $d->vendor()->where('type', $request->type)->select('vendor_id as id', 'name', 'status')->get();
                    foreach ($vend as $vendor) {
                        $d =  Item::where('vendor_id', $vendor->id)
                        ->where(function($query) use ($request){
                            $query->where('name', 'LIKE', '%'.$request->name.'%')
                            ->orWhere('description', 'LIKE', '%'.$request->name.'%');
                        })
                        ->select('name', 'available', 'id', 'image', 'price', 'vendor_name', 'category_id')
                        ->withCount('main_option')
                        ->get();
                        if (count($d) > 0) {
                            Arr::add($d, 'vendor', $vendor->name);
                            Arr::add($d, 'status', $vendor->status);
                            array_push($items, $d);
                            
                        }
                    }
                    
                    return $response = [
                        'vendors' => $vendors,
                        'items' => $items
                    ];
                }
            }
    public function search(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required|string',
            'type' => 'required|string',
            ]);
            if (!$validator) {
                return response(['errors' => $validator->errors()->all()], 422);
            } else {
        $d = Areas::find($request->id);
        
        $vendors = $d->vendor()
                    ->where('type', $request->type)
                    ->where('name', 'like', '%' . $request->name . '%')
                    ->select('name', 'type', 'status', 'image')
                    ->distinct()
                    ->get();
                    $items = array();
                    $vend = $d->vendor()->where('type', $request->type)->select('vendor_id as id', 'name', 'status')->get();
                    foreach ($vend as $vendor) {
                        $d =  Item::where('vendor_id', $vendor->id)
                        ->where(function($query) use ($request){
                            $query->where('name', 'LIKE', '%'.$request->name.'%')
                            ->orWhere('description', 'LIKE', '%'.$request->name.'%');
                        })
                        ->select('name', 'available', 'id', 'image', 'price', 'vendor_name', 'category_id')
                        ->withCount('main_option')
                        ->get();
                        if (count($d) > 0) {
                            Arr::add($d, 'vendor', $vendor->name);
                            Arr::add($d, 'status', $vendor->status);
                            array_push($items, $d);
                            
                        }
                    }
                    
                    return $response = [
                        'vendors' => $vendors,
                        'items' => $items
                    ];
                }
            }
            public function searchVendor(Request $request)
            {
                $validator = Validator::make($request->all(), [
                    'name' => 'required|string',
                    ]);
                    if (!$validator) {
                        return response(['errors' => $validator->errors()->all()], 422);
                    } else {
                        $items = Item::where('vendor_id', $request->id)
                        ->where(function($query) use ($request){
                            $query->where('name', 'LIKE', '%'.$request->name.'%')
                            ->orWhere('description', 'LIKE', '%'.$request->name.'%');
                        })
                        ->withCount('main_option')
                        ->select('name', 'available', 'id', 'image', 'price', 'category_id', 'vendor_name')
                        ->distinct()
                        ->get();
                
        $response = [
            'items' => $items
        ];
        return response()->json($response);
    }
    }
    public function vendorpage(Request $request)
    {
        $tag = '';
        if(isset($request->type)){
            $tag = $request->id.'_'.$request->type;
        }else{
            $tag = $request->name.'_'.$request->id;
        }
        // Cache::flush();
        $value = Cache::tags(['page', $request->name])->remember('vendor_'.$tag, Carbon::now()->addHours(24), function () use ($request, $tag) {
        if(isset($request->type)){
            $d = Areas::find($request->id);
            $vendor = $d->vendor()
            ->where('type', $request->type)
            ->where('status', true)
            ->with([
            'categories' => function ($query){
                $query->withCount('items')
                ->orderBy('items_count', 'desc');
            },
            'area' => function ($query) use ($request) {
                $query->where('areas_id', $request->id);
            }])
            ->withCount('reviews')
            // ->select('id as vendor_id', 'name', 'type', 'status', 'image')
            ->first();
         } else{ 
        $vendor = Vendor::where('name', urldecode($request->name))
        ->with([
        'categories' => function ($query){
            $query->withCount('items')
            ->orderBy('items_count', 'desc');
        }, 'area' => function ($query) use ($request) {
            $query->where('areas_id', $request->id);
        }])
        ->withCount('reviews')
        ->first();
        $t = $vendor->reviews()->avg('rating');
        data_fill($vendor, 'rate', $t);
        // $vendor->makeHidden(['created_at', 'updated_at', 'place_id', 'account_number', 'phone', 'branch', 'account_name', 'bank_name', 'instagram', 'twitter']);
    }
    if ($vendor) {
        $vendor->makeHidden(['created_at', 'updated_at', 'place_id', 'account_number', 'phone', 'branch', 'account_name', 'bank_name', 'instagram', 'twitter']);
    }
       return  $response = [
            'vendor' => $vendor,
            'tag' => $tag
        ];
    });
        return response()->json($value);
    }
    public function vendoritems(Request $request)
    {
        $value = Cache::tags(['category_items'])->remember('category_items_'.$request->cat_id, Carbon::now()->addMinutes(60 * 24), function () use ($request) {
        $cat = Category::find($request->cat_id);
        $items = $cat->items()->orderBy('name')->with(['main_option'])->get();
        $items->makeHidden(['cost_price', 'mark_up_price']);
        return $response = [
            'items' => $items,
        ];
    });
        return response()->json($value);
    }
    
    public function vendoritem(Request $request)
    {
        $value = Cache::tags(['item'])->remember('item_'.$request->cat.'_'.$request->name, Carbon::now()->addMinutes(60 * 24), function () use ($request) {

        $cat = Category::find($request->cat);
        $item = $cat->items()->where('name', urldecode($request->name))
        ->with('main_option')->first();
        if (isset($request->full)) {
            # code...
            $item->makeHidden(['cost_price', 'mark_up_price']);
        }else{

            $item->makeHidden(['cost_price', 'mark_up_price', 'address', 'ig', 'poll', 'promo', 'tel', 'reviews_count', 'sold', 'avg_rating', 'status']);
        }
        return $response = [
            'item' => $item,
            'vendor' => $cat->vendor->name,
            'type' => $cat->vendor->type
        ];
    });
        return response()->json($value);
    }
    public function home(Request $request)
    {
        return 'success';

    }
    public function support(Request $request)
    {
        return view('welcome');

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
