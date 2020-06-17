<?php

namespace App\Http\Controllers;

use App\Transactions;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Validator;
class TransactionsController extends Controller
{
    public function set(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'amount' => 'required|integer|min:1000',
            'type' => 'required|string',
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else {
            $user = Auth::user();
            $digits = 6;
            $rand_code = rand(pow(10, $digits - 1), pow(10, $digits) - 1);

            $trans = new Transactions;
            $trans->amount = $request->amount;
            $trans->type = $request->type;
            $trans->transaction = $request->transaction;
            $trans->reference = $request->reference;
            $trans->transaction_id = $rand_code;
      
            if (($request->type === 'debit')) {
                $user->decrement('wallet', $request->amount);
            } else if (($request->type === 'credit')) {
                $user->increment('wallet', $request->amount);
            }
        
            $trans->user()->associate($user);
            $user->save();
            $trans->save();

     

            $response = [
                'message' => 'success'
            ];
            return response()->json($response);
        }
    }
}
