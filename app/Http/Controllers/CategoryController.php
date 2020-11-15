<?php

namespace App\Http\Controllers;

use App\Category;
use App\Vendor;
use Illuminate\Support\Facades\Auth;
use Validator;

use Illuminate\Http\Request;

class CategoryController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all()
    {
        $vendor = Auth::user()->vendor->categories;
        return response([
            'status' => 'success',
            'menu' => $vendor
        ], 200);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'content' => 'required|string',
            'vendor_id' => 'required'
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        }else {
            $vendor = Auth::user()->vendor;
            $category = new Category;
            $category->name = $request->content;
            $category->vendor_name = $request->vendor_name;
            $vendor->categories()->save($category);
        }
        $response = [
            'category' => $vendor->categories,
        ];
        return response()->json($response);

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function show(Category $category)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $category = $vendor->categories->find($request->id);
        $category->name = $request->content;
        if (isset($request->tag)) {
            # code...
            $category->tag = $request->tag;
        }
        $category->save();
        $response = [
            'category' => $category,
        ];
        return response()->json($response);

    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Category  $category
     * @return \Illuminate\Http\Response
     */
    public function delete(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $category = $vendor->categories->find($request->id);
        $category->delete();
        $response = [ 
            'category' => $category,
        ];
        return response()->json($response);
}
}
