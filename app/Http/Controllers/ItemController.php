<?php

namespace App\Http\Controllers;

use App\Item;
use App\Vendor;
use Illuminate\Support\Facades\Auth;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Support\Facades\Storage;
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
        $categories = $vendor->categories()->with('items.main_option')->find($request->id);

        $response = [
            'items' => $categories
        ];
        return response()->json($response);
    }
    public function count_orders(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $count = $vendor->categories->find($request->cat_id)
                    ->items()->find($request->id)->order()
                    ->with('items')->where('status', 4)->get();

        $n = 0;
        foreach ($count as $value) {
          $n +=  $value['items']->where('id', $request->id)->sum(function ($product) {
                return $product['pivot']['qty'];
            });
        }
        $response = [
            'count' => $n
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
                'files.*' => 'image|mimes:jpeg,JPG,png,jpg,gif,svg|max:4048'
                ]);
                if ($files) {
                    foreach ($files as $file) {
                        
                        $name = time() . $file->getClientOriginalName();
                        $filePath = 'images/' . $name;
                        Storage::disk('s3')->put($filePath, file_get_contents($file));
                        $item->image = "https://edeybucket.s3.us-east-2.amazonaws.com/images/" . $name;
                    }
                }
                $category->items()->save($item);
                if ($comp) {
                    foreach ($comp as $compa) {
                        $item->main_option()->attach($compa, ['type' => 'compulsory']);
                    }
                    # code...
                }
                if ($opt) {
                    foreach ($opt as $opta) {
                        # code...
                        $item->main_option()->attach($opta, ['type' => 'optional']);
                    }
                }
        }
        $response = [
            'message' => 'item added successfully',
        ];
        return response()->json($response);
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\item  $item
     * @return \Illuminate\Http\Response
     */

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
        $item->main_option()->detach();
        $item->name = $request->name;
        $item->price = $request->price;
        $item->description = $request->description;
        $comp = json_decode($request->compulsory);
        $opt = json_decode($request->optional);
        if ($comp) {
            foreach ($comp as $compa) {
                $item->main_option()->attach($compa, ['type' => 'compulsory']);
            }
            # code...
        }
        if ($opt) {
            foreach ($opt as $opta) {
                # code...
                $item->main_option()->attach($opta, ['type' => 'optional']);
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
                'files.*' => 'image|mimes:jpeg,JPG,png,jpg,gif,svg|max:4048'
            ]);
            foreach ($files as $file) {
                $image_name = $file->getRealPath();
                Cloudder::upload($image_name, null, array("width" => 400, "height" => 400, "crop" => "fit", "quality" => "auto", "fetch_format" => "auto"));
                $image_url = Cloudder::show(Cloudder::getPublicId(), ["width" => 400, "height" => 400]);
                $item->image = $image_url;
                $item->save();
            }
        }
        $response = [
            'message' => 'image edited successfully',
        ];
        return response()->json($response);
    }
    
    public function available(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $category = $vendor->categories->find($request->cat_id);
        $item = $category->items->find($request->item_id);
        $item->available= $request->availability;
        $item->save();
        if ($item->available) {
            $d = 'on';
        } else {
           $d = 'off';
        }
        
        $response = [
            'message' => $item->name. ' has been turned '. $d,
        ];
        return response()->json($response);
    }
    public function delete(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $category = $vendor->categories->find($request->cat_id);
        $item = $category->items->find($request->item_id);
        $item->main_option()->detach();
        $item->delete();
        $response = [
            'message' => 'Successfully deleted',
        ];
        return response()->json($response);
    }
}
