<?php

namespace App\Http\Controllers;
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Illuminate\Http\Request;

class Admin extends Controller
{
    public function get_users(){
        $users = User::where('status', 2)->select('id', 'role', 'first_name', 'middle_name', 'last_name', 'city', 'area', 'phone', 'created_at', 'updated_at', 'wallet')
        ->withCount('orders')
        ->get();
        $response = [
            'users' => $users
        ];
        return response()->json($response);
    }
}
