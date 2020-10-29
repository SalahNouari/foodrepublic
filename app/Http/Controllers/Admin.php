<?php

namespace App\Http\Controllers;
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request;

class Admin extends Controller
{
    public function del_user(Request $request){
        $users = User::find($request->id);
    
        $users->delete();
        $response = [
            'deleted' => 'deleted'
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
