<?php

namespace App\Http\Controllers;

use App\Areas;
use App\Vendor;
use App\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;  
use Illuminate\Support\Facades\Response;


class MainController extends Controller
{
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
        $items = Vendor::where('name', $request->name)->first()->categories()->with('items')->get();
        $response = [
            'items' => $items,
        ];
        return response()->json($response);
    }
    public function vendoritem(Request $request)
    {
        $item = Category::find($request->cat)->items()->where('name', $request->name)->first();
        $response = [
            'item' => $item,
        ];
        return response()->json($response);
    }
 
}
