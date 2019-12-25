<?php

namespace App\Http\Controllers;

use App\Option;
use Illuminate\Support\Facades\Auth;
use JD\Cloudder\Facades\Cloudder;
use Validator;
use Illuminate\Http\Request;

class OptionController extends Controller
{
    public function delete(Request $request)
    {
        $option = Auth::user()->vendor->option->find($request->id);
        $option->delete();
        $response = [
            'option' => $option,
        ];
        return response()->json($response);
    }
    public function all(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $response = [
            'options' => $vendor->option()->select('id', 'name', 'price', 'image', 'status', 'available')->get(),
            'list' => $vendor->option->pluck('name')
        ];
        return response()->json($response);
    }
    public function update(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'id' => 'required',
            'price' => 'required'
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else {
            $option = $vendor->option->find($request->id);
            $option->name = $request->name;
            $option->price = $request->price;
            $option->save();
        }
        $response = [
            'message' => 'Item edited successfully',
        ];
        return response()->json($response);
    }

    public function available(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $option = $vendor->option->find($request->id);
        $option->available = $request->availability;
        $option->save();
        if ($option->available) {
            $d = 'on';
        } else {
            $d = 'off';
        }

        $response = [
            'message' => $option->name . ' has been turned ' . $d,
        ];
        return response()->json($response);
    }

    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required'
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else {
            $vendor = Auth::user()->vendor;
            $option = new Option();
            $option->name = $request->name;
            $option->price = $request->price;
            $files = $request->file('files');
            request()->validate([
                'files' => 'required',
                'files.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);

            foreach ($files as $file) {
                $image_name = $file->getRealPath();
                Cloudder::upload($image_name, null, array("width" => 400, "height" => 400, "crop" => "fit", "quality" => "auto", "fetch_format" => "auto"));
                $image_url = Cloudder::show(Cloudder::getPublicId(), ["width" => 400, "height" => 400]);
                $option->image = $image_url;
                $vendor->option()->save($option);
            }
        }
        $response = [
            'extras' => $vendor->option,
        ];
        return response()->json($response);
    }
    public function image(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else {
            $option = Auth::user()->vendor->option->find($request->id);
            $files = $request->file('files');
            request()->validate([
                'files' => 'required',
                'files.*' => 'image|mimes:jpeg,png,jpg,gif,svg|max:2048'
            ]);
            foreach ($files as $file) {
                $image_name = $file->getRealPath();
                Cloudder::upload($image_name, null, array("width" => 400, "height" => 400, "crop" => "fit", "quality" => "auto", "fetch_format" => "auto"));
                $image_url = Cloudder::show(Cloudder::getPublicId(), ["width" => 400, "height" => 400]);
                $option->image = $image_url;
                $option->save();
            }
        }
        $response = [
            'option' => $option,
        ];
        return response()->json($response);
    }
}
