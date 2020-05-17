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
use Carbon\Carbon;

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
        $order = Auth::user()->delivery_agent->orders()
        ->select('id', 'address_id', 'payment_method', 'delivery_status', 'tracking_id', 'created_at', 'updated_at', 'status')
        ->with(['address' => function ($query) {
            $query->select('id', 'lat', 'lng', 'name');
        }])
        ->where('updated_at', '>=', Carbon::now()->subDays(2))
        // where todays date
        ->latest()->paginate(20);
        $response = [
            'orders' => $order
        ];
        return response()->json($response);
         
    }
    public function save(Request $request)
    {
        $validator = Validator::make($request->all(), [
        'total' => 'required|integer',
        'items' => 'required',
        'address' => 'required|string',
        'wallet' => 'required|boolean',
        'grand_total' => 'required|integer',
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
            $order->wallet = $request->wallet;
            $order->delivery_fee = $request->delivery_fee;
            $order->payment_method = $request->payment_method;
            $order->total = $request->total;
            $payM = $request->payment_method;
            if(($payM != 4) && ($order->wallet === true) && ($order->paid === true)){
                if ($user->wallet >= $request->grand_total) {
                $user->decrement('wallet', $request->grand_total);
                $user->save();
                } else{
                    return response(['message' => 'Insufficient funds in wallet'], 422);
                }
                
            }
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

                $order->items()->attach($itm['id'], ['qty' => $itm['qty'], 'total' => $item['total'], 'tracking_id' => $random_code, 'vendor_id' => $request->vendor_id]);
  
                foreach ($comp as $compa) {
                    $order->options()->attach($compa['id'], ['type' => $compa['type'], 'qty' => 1, 'tracking_id' => $random_code, 'vendor_id' => $request->vendor_id]);
                }
            
                foreach ($opt as $opta) {
                    # code...
                    $order->options()->attach($opta['id'], ['type' =>  $opta['type'], 'qty' => $opta['qty'], 'tracking_id' => $random_code, 'vendor_id' => $request->vendor_id]);
                }
            }
            
            $response = [
                'order' => Order::where('id', $order['id'])->with(['items', 'options'])->get()
            ];
            return response()->json($response);
            
        }

    }
    public function saveOffline(Request $request)
    {
        $ordersList = json_decode($request->orders);
        foreach ($ordersList as $o) {
            $vendor = Auth::user();
            $items = $o->i;
            $digits = 6;
            $rand_code = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
            $order = new Order;
            $order->tracking_id = $rand_code;
            $order->grand_total = $o->t;
            if ($o->n) {
                $order->table_no = $o->n;
            }
            if ($o->c) {
                $order->change_amount = $o->c;
            }
            if ($o->m) {
                $order->payment_method = 3;
            } else{
                $order->payment_method = 6;
            }
            $order->paid = $o->p;
            $order->total = $order->t;
            $order->status = 4;
            $order->user_status = 0;
            $order->paid = 1;
            $order->recieved_time = $o->d;
            $order->served_time = $o->d;
            $order->delivered_time = $o->d;
            $order->vendor()->associate($vendor);
            if ($o->u) {
                $user = User::find($o->u);
                $order->user()->associate($user);
            } else{
                $order->user()->associate($vendor);

            }

            $order->save();
        
            foreach ($items as $item) {
                $digits = 8;
                $random_code = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
        
                $comp = $item['options']['compulsory'];
                $opt = $item['options']['optional'];
                $itm = $item;

                $order->items()->attach($itm['id'], ['qty' => $itm['qty'], 'total' => ($item['qty'] * $item['price']), 'tracking_id' => $random_code, 'vendor_id' => $request->vendor_id]);
  
                foreach ($comp as $compa) {
                    $order->options()->attach($compa['id'], ['type' => $compa['type'], 'qty' => 1, 'tracking_id' => $random_code, 'vendor_id' => $request->vendor_id]);
                }
            
                foreach ($opt as $opta) {
                    # code...
                    $order->options()->attach($opta['id'], ['type' =>  $opta['type'], 'qty' => $opta['qty'], 'tracking_id' => $random_code, 'vendor_id' => $request->vendor_id]);
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
        $order = Auth::user()->delivery_agent->orders()->with(['user', 'items', 'options', 'address.area', 'delivery'])->find($request->id);
      
        $response = [
            'order' => $order
        ];
        return response()->json($response);
    }
    public function find(Request $request)
    {
        $order = Auth::user()->vendor->orders()->with(['user'=> function ($query) {
                $query->select('id', 'first_name', 'phone', 'middle_name', 'surname', 'image');
        }, 'items'=> function ($query) {
                $query->select('item_id','order_id', 'price', 'name', 'image');
        }, 'options', 'address.area', 'delivery', 'reviews'])->find($request->id);
      
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
        $order->recieved_time = Carbon::now();
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
        $user = $order->user;
        $user()->increment('orders');
        $user()->increment('points', 10);
        $order->served_time = Carbon::now();
        
        $order->user_status = 0;
        $order->save();
        $response = [
            'message' => 'Your order has been served',
            'token' => $user->token,

        ];
        return response()->json($response);
    }
    public function transit(Request $request)
    {
        $order = Auth::user()->vendor->orders()->find($request->id);
        $order->status = 3;
        $order->user_status = 0;
        $order->reject_reason = '';
        $user = $order->user;
        $order->delivery_status = 0;
        $order->transit_time = Carbon::now();

        $agent = Delivery::find($request->delivery_agent_id);
        $order->delivery()->associate($agent);
        $order->save();
        $response = [
            'message' => 'Your order is on the way',
            'token' => $user->token,
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
        $order->delivered_time = Carbon::now();
        
        $order->user()->increment('points', 10);
        $order->status = 4;
        $order->user_status = 0;
        $order->paid = 1;
        $user = $order->user;
        $order->delivery_status = 0;
        
        $order->save();
        $response = [
            'message' => 'Your order has been delivered.',
            'token' => $user->token        ];
        return response()->json($response);
    }
    public function rejected(Request $request)
    {
        $order = Auth::user()->vendor->orders()->find($request->id);
        $order->status = 5;
        $order->user_status = 0;
        $order->rejected_time = Carbon::now();
        $user = $order->user;
        $order->delivery_status = 0;
        $order->reject_reason = $request->reason;
        if($request->delivery_agent_id != null){
            $agent = Delivery::find($request->delivery_agent_id);
            $order->delivery()->dissociate($agent);
        }
        if($order->paid){
            $order->user()->increment('wallet', $order->grand_total);
        }
        $order->save();
        $response = [
            'message' => 'Your order has been canceled',
            'token' => $user->token
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

