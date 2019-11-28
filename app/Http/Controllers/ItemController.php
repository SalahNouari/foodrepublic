<?php

namespace App\Http\Controllers;

use App\item;
use App\Vendor;
use Illuminate\Support\Facades\Auth;
use JD\Cloudder\Facades\Cloudder;
use Validator;

use Illuminate\Http\Request;

class ItemController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function all(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $menu = $vendor->categories()->with('items')->paginate(8);

        $response = [
            'items' => $menu
        ];
        return response()->json($response);
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
            'name' => 'required|string',
            'price' => 'required',
            'category_name' => 'required',
            'description' => 'required',
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else {
            $vendor = Auth::user()->vendor;
            $category = $vendor->categories->find($request->category_id);
            $item = new Item;
            $item->name = $request->name;
            $item->price = $request->price;
            $item->description = $request->description;
            $item->category_name = $request->category_name;
            $item->category_id = $request->category_id;
            $item->generic = $request->category_name;
            $item->vendor_name = $vendor->name;
            $item->vendor_id = $vendor->id;
            $comp = json_decode($request->compulsory);
            $opt = json_decode($request->optional);
            $files = $request->file('files');
            request()->validate([
                'files' => 'required',
                'files.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
                ]);
                if ($files) {
                    foreach ($files as $file) {
                        $image_name = $file->getRealPath();
                        Cloudder::upload($image_name, null, array("width" => 400, "height" => 400, "crop" => "fit", "quality" => "auto", "fetch_format" => "auto"));
                        $image_url = Cloudder::show(Cloudder::getPublicId());
                        $item->image = $image_url;
                    }
                }
                $category->items()->save($item);
                if ($comp) {
                    foreach ($comp as $compa) {
                        $item->option()->attach($compa, ['type' => 'compulsory']);
                    }
                    # code...
                }
                if ($opt) {
                    foreach ($opt as $opta) {
                        # code...
                        $item->option()->attach($opta, ['type' => 'optional']);
                    }
                }
        }
        $response = [
            'message' => 'item added successfully',
        ];
        return response()->json($response);
        // return response()->json((is_array($comp)));
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\item  $item
     * @return \Illuminate\Http\Response
     */
    public function show(item $item)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\item  $item
     * @return \Illuminate\Http\Response
     */
    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\item  $item
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $item = $vendor->categories->find($request->cat_id)->items()->find($request->item_id);
        $item->option()->detach();
        $item->name = $request->name;
        $item->price = $request->price;
        $item->description = $request->description;
        $comp = json_decode($request->compulsory);
        $opt = json_decode($request->optional);
        if ($comp) {
            foreach ($comp as $compa) {
                $item->option()->attach($compa, ['type' => 'compulsory']);
            }
            # code...
        }
        if ($opt) {
            foreach ($opt as $opta) {
                # code...
                $item->option()->attach($opta, ['type' => 'optional']);
            }
        }
        $item->save();
        $response = [
            'message' => 'Edited successfully',
        ];
        return response()->json($response);
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\item  $item
     * @return \Illuminate\Http\Response
     */
    public function image(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else {
            $vendor = Auth::user()->vendor;
            $item = $vendor->categories->find($request->cat_id)->items()->find($request->item_id);
            $files = $request->file('files');
            request()->validate([
                'files' => 'required',
                'files.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
            foreach ($files as $file) {
                $image_name = $file->getRealPath();
                Cloudder::upload($image_name, null, array("width" => 400, "height" => 400, "crop" => "fit", "quality" => "auto", "fetch_format" => "auto"));
                $image_url = Cloudder::show(Cloudder::getPublicId());
                $item->image = $image_url;
                $item->save();
            }
        }
        $response = [
            'message' => 'image edited successfully',
        ];
        return response()->json($response);
    }
    public function delete(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $category = $vendor->categories->find($request->cat_id);
        $item = $category->items->find($request->item_id);
        $item->option()->detach();
        $item->delete();
        $response = [
            'message' => 'Successfully deleted',
        ];
        return response()->json($response);
    }
}
