<?php

namespace App\Http\Controllers;
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
        $user = User::where('phone', $request->phone); 
            if($user){ 
            $success['token'] =  $user->createToken('MyApp')->accessToken; 
            $success['user'] =  $user; 
            return response()->json(['success' => $success], $this->successStatus); 
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
