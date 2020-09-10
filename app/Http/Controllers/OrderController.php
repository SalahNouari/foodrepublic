<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Address;
use App\Delivery;
use Illuminate\Support\Facades\Auth;
use Validator;
use App\Events\VendorEvent;
use App\Events\OrderEvent;
use App\Order; 
use App\User;
use App\Vendor;
use Carbon\Carbon;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Cache;


class OrderController extends Controller
{
    public function all(Request $request)
    {
        $order = Auth::user()->vendor->orders()->latest()->paginate(12);
        $data = $order->makeHidden(['change_amount', 'distance', 'duration', 'grand_total', 'service_charge', 'delivery_fee', 'delivered_time', 'recieved_time', 'reject_reason', 'rejected_time', 'served_time', 'transit_time', 'table_no', 'total']);
        $order->data = $data;
        $response = [
            'orders' => $order
        ];
        return response()->json($response);
    }

    public function alldelivery_find(Request $request)
    {
       $order = $this->getOrder_find($request->id);
        $response = [
            'orders' => $order
        ];
  
        return response()->json($response);
    }

    public function getOrder_find($id){
       $value = Cache::remember('order_find_'.$id, Carbon::now()->addHours(24), function () use ($id) {
        $order = Order::where('id', $id)->select('id', 'vendor_id', 'address_id', 'delivery_status', 'created_at', 'updated_at', 'status')
        ->with(['address' => function ($query) {
            $query->select('id', 'lat', 'lng', 'name');
        },'vendor' => function ($query) {
            $query->select('id','name', 'address', 'lat', 'lng');
        }])
        ->first();
        return $order;
    });
    return $value;
    }
    public function alldelivery()
    {
        $order = Order::where('status', 2)->select('id', 'vendor_id', 'address_id', 'delivery_status', 'created_at', 'updated_at', 'status')
        ->with(['address' => function ($query) {
            $query->select('id', 'lat', 'lng', 'name');
        },'vendor' => function ($query) {
            $query->select('id','name', 'address', 'lat', 'lng');
        }])
        ->where('updated_at', '>=', Carbon::now()->subDays(2))
        // where todays date
        ->latest()->paginate(20);
        $response = [
            'orders' => $order
        ];
        return response()->json($response);
    }
        

    public function all_my_delivery()
    {
        $order = Auth::user()->delivery_agent->orders()
        ->select('id', 'address_id', 'delivery_status', 'created_at', 'updated_at', 'status')
        ->with(['address' => function ($query) {
            $query->select('id', 'lat', 'lng', 'name');
        },'vendor' => function ($query) {
            $query->select('id','name', 'address', 'lat', 'lng');
        }])
        ->where('updated_at', '>=', Carbon::now()->subDays(2))
        // where todays date
        ->latest()->paginate(100);
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
            if(isset($request->table_no)){
                $order->table_no = $request->table_no;
            }
            if($payM != 4){
                $address = Address::find($request->address_id);
                $order->address()->associate($address);
            }
            $order->vendor()->associate($vendor);
            $order->user()->associate($user);
            
          
            if ($request->discount && isset($request->d_id)) {
                $delivery_agent = Delivery::find($request->d_id);
                $order->delivery()->associate($delivery_agent);
                $order->discount = true;
                $order->status = 3;
                $order->recieved_time = Carbon::now();
                $order->served_time = Carbon::now();
                $order->transit_time = Carbon::now();


            }
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
        $list = array();
        $vendor = Auth::user();
        foreach ($ordersList as $o) {
            array_push($list, $o->o);
            $items = $o->i;
            $digits = 6;
            $rand_code = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
            $order = new Order;
            $order->tracking_id = $rand_code;
            $order->grand_total = $o->t;
            if (isset($o->n)) {
                $order->table_no = $o->n;
            }
            if (isset($o->c)) {
                $order->change_amount = $o->c;
            }
            if (isset($o->m)) {
                $order->payment_method = 3;
            } else{
                $order->payment_method = 6;
            }
            $order->paid = $o->p;
            $order->total = $o->t;
            $order->offline = $o->o;
            $order->status = 4;
            $order->user_status = 0;
            $order->paid = 1;
            $time = Carbon::parse($o->d);
            $order->recieved_time = $time;
            $order->served_time = $time;
            $order->updated_at = $time;
            $order->delivered_time = $time;
            $order->vendor()->associate($vendor);
            if (isset($o->u)) {
                $user = User::find($o->u);
                $order->user()->associate($user);
            } else{
                $order->user()->associate($vendor);

            }

            $order->save();
        
            foreach ($items as $item) {
                $digits = 8;
                $random_code = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
        
                $comp = $item->options->compulsory;
                $opt = $item->options->optional;
                $itm = $item;

                $order->items()->attach($itm->id, ['qty' => $itm->qty, 'total' => ($item->qty * $item->price), 'tracking_id' => $random_code, 'vendor_id' => $vendor->id]);
  
                foreach ($comp as $compa) {
                    $order->options()->attach($compa->id, ['type' => 'compulsory', 'qty' => 1, 'tracking_id' => $random_code, 'vendor_id' => $vendor->id]);
                }
            
                foreach ($opt as $opta) {
                    # code...
                    $order->options()->attach($opta->id, ['type' =>  'optional', 'qty' => $opta->qty, 'tracking_id' => $random_code, 'vendor_id' => $vendor->id]);
                }
            }
            
            
        }
            $response = [
                'message' => 'successful',
                'list' => $list 
            ];
            return response()->json($response);

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
        $order = Order::with(['user', 'items', 'options', 'address.area', 'vendor'=> function ($query) {
            $query->select('id', 'name', 'address');
        }, 'delivery'])
        ->find($request->id);
        $response = '';
          if (($order->delivery_id === null) || ($order->delivery_id == $request->delivery_agent_id)) {
              $response = [
                  'order' => $order
                ];
            }else{
                
                $response = [
                 'order' => null
             ];
    }
    return response()->json($response);
     
    }
    public function find(Request $request)
    {
        $order = Auth::user()->vendor->orders()->with(['user'=> function ($query) {
                $query->select('id', 'first_name', 'phone', 'middle_name', 'surname', 'image');
        }, 'items'=> function ($query) {
                $query->select('item_id','order_id', 'price', 'name', 'image');
            }, 'options', 'address.area', 'delivery' => function ($query) {
                $query->select('id', 'name', 'phone');

        }, 'reviews'])->find($request->id);
      
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
        $order = Order::find($request->id);
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
        // if ($order->status === 1) {
        $order->status = 2;
        $order->served_time = Carbon::now();
        $order->delivery_status = 0;
        $order->user_status = 0;
        $user = $order->user;
        $agent = Delivery::find($request->delivery_agent_id);
        $order->save();
        $this->getOrder_find($order->id);
        $response = [
            'message' => 'Your order has been accepted',
            'token' => $user->token,
            'agentsToken' => $agent->token
        ];
        event(new OrderEvent($order));
        return response()->json($response);
    // } else {
    //     return response('error', 400);
    // }
    }
    public function transit(Request $request)
    {
        $order = Order::find($request->id);
        // if ($order->status === 2) {
            # code...
            $order->status = 3;
            $order->user_status = 0;
            $order->reject_reason = '';
            $user = $order->user;
            $order->delivery_status = 0;
            $order->transit_time = Carbon::now();
    
            $agent = Delivery::find($request->delivery_agent_id);
            $order->delivery()->associate($agent);
            $vendor = $order->vendor;
            $d_id = $request->delivery_agent_id;
            $vendorId = $vendor->id;
            $vendorToken = $vendor->token;
            $area = $order->address->area->id;
            $order->save();
            event(new OrderEvent($order));
            if (Cache::has('order_find_'.$order->id)) {
                # code...
                Cache::forget('order_find_'.$order->id);
            }
            if (!Cache::has('vendor_timer_'.$vendorId)) {
                  $this->Start_timer($vendorId, $vendor, $area, $d_id);
                }
            $response = [
                'message' => 'Your order is on the way',
                'message2' => 'Prepare this order, delivery agent is on the way',
                'token' => $user->token,
                "vendorToken" => $vendorToken,
            ];
            return response()->json($response);
        // } else {
        //     return response('error', 400);
        // }
    }
    public function Start_timer($vendorId, $vendor, $area, $d_id)
    {

        $time = Carbon::now()->addMinutes(5);
        $time2 = Carbon::now()->addHours(24);
        $vendor2 = [
          'image' => $vendor->image,
          'id' => $vendor->id,
          'name' => $vendor->name,
          'd_id' => $d_id,
          'expire' => $time
      ];
      Cache::remember('vendor_timer_'.$vendorId, $time, function () use ($vendor, $vendor2, $area, $time2, $time) {
         $this->real_time($area, $vendor2, $time2);
            return response()->json($vendor2);
        });
        event(new VendorEvent($vendor, $time, $area, $d_id));
    }

    public function get_real_time(Request $request) {
        $val1 = Cache::get('area_timer_'.$request->area);
        if (is_array($val1)) {
            $dat =  array_filter($val1, function($val){
                return 1 < Carbon::parse(Carbon::now())->floatDiffInSeconds($val['expire'], false);
            });
        }
        if (isset($dat)) {
            # code...
            $res = $dat;
        } else {
            $res = $val1;
        }
        return($res);
    }
    public function real_time($area, $vendor2, $time2) {
        $data = array();
        $val1 = Cache::get('area_timer_'.$area);
        if (isset($val1)) {
            $filtered = array_filter($val1, function($val){
                return 1 < Carbon::parse(Carbon::now())->floatDiffInSeconds($val['expire'], false);
            });
            array_push($filtered, $vendor2);
            Cache::put('area_timer_'.$area, $filtered, $time2);
            # code...
        }else{
            array_push($data, $vendor2);
         Cache::put('area_timer_'.$area, $data, $time2);
        }
    }
    public function delivered(Request $request)
    {
        $order = Order::find($request->id);

        if ($order->status === 3) {
        $order->user()->increment('orders');
        if(!$order->served_time){
            $order->served_time = Carbon::now();
        }
        $order->delivered_time = Carbon::now();
        
        $order->user()->increment('points', 10);
        $order->status = 4;
        $order->user_status = 0;
        $order->paid = 1;
        $user = $order->user;
        $order->delivery_status = 0;
        $vendorToken = $order->vendor->token;
        
        $order->save();
        $response = [
            'message' => 'Your order has been delivered.',
            'message2' => 'Order Delivered',
            'token' => $user->token,
            "vendorToken" => $vendorToken ];
        return response()->json($response);
    } else {
        return response('error', 400);
    }
    }
    
    public function rejected(Request $request)
    {
        $order = Auth::user()->vendor->orders()->find($request->id);

        if ($order->status === 1) {
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
    } else {
        return response('error', 400);
    }
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

