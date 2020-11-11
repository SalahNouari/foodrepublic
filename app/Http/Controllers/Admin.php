<?php

namespace App\Http\Controllers;

use App\Delivery;
use App\Order;
use App\User;
use App\Vendor;
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class Admin extends Controller
{
    public function empty_wallet(Request $request){
        $user = User::find($request->id);
       
        $user->wallet = 0;
        $user->save();
        $response = [
            'deleted' => 'deleted'
        ];
        return response()->json($response);
    }
    public function auth_user(Request $request){ 
        $user = User::where('phone', $request->phone)->first(); 
            if($user){ 
            $success['token'] =  $user->createToken('MyApp')->accessToken; 
            $success['user'] =  $user; 
            return response()->json(['success' => $success], 200); 
        }
        else{
            return response()->json(['error'=>'Invalid phone number.'], 400); 
        } 
    }

    public function del_user(Request $request){
        $user = User::find($request->id);
    
        $user->delete();
        $response = [
            'deleted' => 'deleted'
        ];
        return response()->json($response);
    }
    public function get_vendors(){
        $users = Vendor::
        select('id', 'name', 'phone', 'created_at', 'updated_at')
        ->withCount('orders')
        ->withCount([
            'orders AS orders_sum' => function ($query) {
                $query->select(DB::raw("SUM(total) as paidsum"))->where('status', 4);
            }
            ])
            ->with('user')
            ->get();
            $response = [
                'users' => $users
            ];
            return response()->json($response);
        }
        public function get_delivery_agents(){
            $users = Delivery::
            select('id', 'name', 'phone', 'created_at', 'updated_at')
            ->withCount('orders')
            ->withCount([
                'orders AS orders_sum' => function ($query) {
                    $query->select(DB::raw("SUM(total) as paidsum"))->where('status', 4);
                }
                ])
                ->with('user')
        ->get();
        $response = [
            'users' => $users
        ];
        return response()->json($response);
    }
    public function get_users(){
        $users = User::select('id', 'role', 'first_name', 'middle_name', 'surname', 'state_id', 'area_id', 'phone', 'created_at', 'updated_at', 'wallet')
        ->withCount('orders')
        ->get();
        $response = [
            'users' => $users
        ];
        return response()->json($response);
    }
}
