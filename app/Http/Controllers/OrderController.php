<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;
use App\FoodCategory; 
use App\Address;
use App\Delivery;
use App\Item;
use Illuminate\Support\Facades\Auth;
use Validator;

use App\Order; 
use App\Location; 
use App\Snacks; 
use App\User;
use App\Vendor;

class OrderController extends Controller
{
    public function all(Request $request)
    {
        $order = Auth::user()->vendor->orders()->latest()->paginate(12);
        $response = [
            'orders' => $order
        ];
        return response()->json($response);
    }

    public function alldelivery()
    {
        $order = Auth::user()->delivery_agent->orders()->latest()->paginate(12);
        $response = [
            'orders' => $order
        ];
        return response()->json($response);
         
    }
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'total' => 'required',
        'items' => 'required',
        'address' => 'required',
        'grand_total' => 'required',
        'vendor_id' => 'required',
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else {
            $user = Auth::user();
            $vendor = Vendor::find($request->vendor_id);
            $items = $request->items;
            $digits = 6;
            $rand_code = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
        
            $order = new Order;
            $order->duration = $request->duration;
            $order->distance = $request->distance;
            $order->tracking_id = $rand_code;
            $order->grand_total = $request->grand_total;
            $order->change_amount = $request->change_amount;
            $order->service_charge = $request->service_charge;
            $order->paid = $request->paid;
            $order->delivery_fee = $request->delivery_fee;
            $order->payment_method = $request->payment_method;
            $order->total = $request->total;
            $payM = $request->payment_method;
            if(($payM === 4) || ($payM === 5)){
                $order->table_no = $request->table_no;
            }
            if($payM != 4){
                $address = Address::find($request->address_id);
                $order->address()->associate($address);
            }
            $order->vendor()->associate($vendor);
            $order->user()->associate($user);

            $order->save();
        
            foreach ($items as $item) {
                $digits = 8;
                $random_code = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
        
                $comp = $item['compulsory'];
                $opt = $item['optional'];
                $itm = $item['item'][0];

                $order->items()->attach($itm['id'], ['qty' => $itm['qty'], 'total' => $item['total'], 'tracking_id' => $random_code]);
  
                foreach ($comp as $compa) {
                    $order->options()->attach($compa['id'], ['type' => $compa['type'], 'qty' => 1, 'tracking_id' => $random_code]);
                }
            
                foreach ($opt as $opta) {
                    # code...
                    $order->options()->attach($opta['id'], ['type' =>  $opta['type'], 'qty' => $opta['qty'], 'tracking_id' => $random_code]);
                }
            }
            
            $response = [
                'order' => Order::where('id', $order['id'])->with(['items', 'options'])->get()
            ];
            return response()->json($response);
            
        }

    }
    public function paid(Request $request)
    {
        $order = Auth::user()->vendor->orders()->find($request->id);
        $order->paid = true;
        $order->save();
        $response = [
            'message' => 'marked paid successful'
        ];
        return response()->json($response);
    }
    public function delivery_find(Request $request)
    {
        $order = Auth::user()->delivery_agent->orders()->with(['user', 'items', 'options', 'address', 'delivery'])->find($request->id);
      
        $response = [
            'order' => $order
        ];
        return response()->json($response);
    }
    public function find(Request $request)
    {
        $order = Auth::user()->vendor->orders()->with(['user', 'items', 'options', 'address', 'delivery'])->find($request->id);
      
        $response = [
            'order' => $order
        ];
        return response()->json($response);
    }
    public function read(Request $request)
    {
        $order = Auth::user()->vendor->orders()->find($request->id);
        $order->status = 1;
        $order->user_status = 0;
        $order->delivery_status = 0;
        $order->save();
        $response = [
            'message' => 'read successful'
        ];
        return response()->json($response);
    }
    public function delivery_read(Request $request)
    {
        $order = Auth::user()->delivery_agent->orders()->find($request->id);
        $order->delivery_status = 1;
        $order->save();
        $response = [
            'message' => 'read successful'
        ];
        return response()->json($response);
    }
    public function served(Request $request)
    {
        $order = Auth::user()->vendor->orders()->find($request->id);
        $order->status = 2;
        $order->user()->increment('orders');
        $order->user()->increment('points', 10);
        
        $order->user_status = 0;
        $order->save();
        $response = [
            'message' => 'served successful'
        ];
        return response()->json($response);
    }
    public function transit(Request $request)
    {
        $order = Auth::user()->vendor->orders()->find($request->id);
        $order->status = 3;
        $order->user_status = 0;
        $order->reject_reason = '';
        $order->delivery_status = 0;

        $agent = Delivery::find($request->delivery_agent_id);
        $order->delivery()->associate($agent);
        $order->save();
        $response = [
            'message' => 'transit successful'
        ];
        return response()->json($response);
    }
    public function delivered(Request $request)
    {
        $order = '';

        if(Auth::user()->vendor){

            $order = Auth::user()->vendor->orders()->find($request->id);
        } else {
            $order = Auth::user()->delivery_agent->orders()->find($request->id);
        }
        $order->user()->increment('orders');
        $order->user()->increment('points', 10);
        $order->status = 4;
        $order->user_status = 0;
        $order->delivery_status = 0;

        $order->save();
        $response = [
            'message' => 'delivered successfully'
        ];
        return response()->json($response);
    }
    public function rejected(Request $request)
    {
        $order = Auth::user()->vendor->orders()->find($request->id);
        $order->status = 5;
        $order->user_status = 0;
        $order->delivery_status = 0;
        $order->reject_reason = $request->reason;
        if($request->delivery_agent_id != null){
            $agent = Delivery::find($request->delivery_agent_id);
            $order->delivery()->dissociate($agent);
        }
        if($order->paid){
            $user = $order->user;
            $user->wallet += $order->grand_total;
            $user->save();
        }
        $order->save();
        $response = [
            'message' => 'order reject successful'
        ];
        return response()->json($response);
    }

    public function delete(Request $request)
    {
        $order = Auth::user()->vendor->orders()->find($request->id);
        $order->delete();
        $response = [
            'message' => 'deleted successful'
        ];
        return response()->json($response);
    }

}

