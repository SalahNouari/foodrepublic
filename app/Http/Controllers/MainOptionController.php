<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\MainOption;
use Illuminate\Support\Facades\Auth;
use JD\Cloudder\Facades\Cloudder;
use Validator;
class MainOptionController extends Controller
{
  
    public function all()
    {
        $vendor = Auth::user()->vendor;
        $response = [
            'main_options' => $vendor->main_option,
            'list' => $vendor->main_option->pluck('name')
        ];
        return response()->json($response);
    }
    public function delete(Request $request)
    {
        $main_option = Auth::user()->vendor->main_option->find($request->id);
        $main_option->option()->detach();
        $main_option->delete();
        $response = [
            'message' => 'Main option deleted',
        ];
        return response()->json($response);
    }
    public function update(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'id' => 'required',
            'title' => 'required'
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else {
            $main_option = $vendor->main_option->find($request->id);
            $main_option->option()->detach();
            $main_option->name = $request->name;
            $main_option->title = $request->title;
            $main_option->max = $request->max;
            $main_option->save();
            $options = json_decode($request->options);
            if ($options) {
                foreach ($options as $opta) {
                    $main_option->option()->attach($opta);
                }
            }
        }
        $response = [
            'message' => 'Main option edited successfully',
        ];
        return response()->json($response);
    }
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'name' => 'required',
            'title' => 'required',
            'max' => 'required',
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else {
            $vendor = Auth::user()->vendor;
            $main_option = new MainOption();
            $main_option->name = $request->name;
            $main_option->title = $request->title;
            $main_option->max = $request->max;
            $vendor->main_option()->save($main_option);
            $options = json_decode($request->options);
            if ($options) {
                foreach ($options as $opta) {
                    $main_option->option()->attach($opta);
                }
            }
            $response = [
                'message' => 'Saved successfully',
            ];
            return response()->json($response);
        }
    }
}
