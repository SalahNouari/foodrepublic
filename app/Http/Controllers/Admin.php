<?php

namespace App\Http\Controllers;

use App\Order;
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request;

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
        $amountSum = Order::selectRaw('sum(total)')
->whereColumn('orders_id', 'orders.id')
->getQuery();
        $users = User::where('role', 'vendor')
        ->select('id', 'role', 'first_name', 'middle_name', 'surname', 'state_id', 'area_id', 'phone', 'created_at', 'updated_at', 'wallet')
        ->with(['vendor' => function ($query) use ($amountSum) {
            $query->selectSub($amountSum, 'name')
            ->withCount(['orders' => function ($query) {
                $query->where('status', 4);
        }]);
            }])
        ->withCount('orders')
        ->get();
        $response = [
            'users' => $users
        ];
        return response()->json($response);
    }
    public function get_delivery_agents(){
        $users = User::where('role', 'delivery_agent')
        ->select('id', 'role', 'first_name', 'middle_name', 'surname', 'state_id', 'area_id', 'phone', 'created_at', 'updated_at', 'wallet')
        ->withCount('orders')
        ->with(['delivery_agent' => function ($query) {
            $query->select('delivery_agent_id', 'name')
            ->withCount(['orders' => function ($query) {
                $query->where('status', 4);
        }]);
        }])
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
