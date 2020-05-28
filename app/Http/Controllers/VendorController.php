<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Vendor;
use App\Menu;
use App\Tag;
use App\Area;
use App\Reviews;
use JD\Cloudder\Facades\Cloudder;
use Illuminate\Support\Facades\Auth;
use AfricasTalking\SDK\AfricasTalking;

use App\Http\Controllers\Controller;
use Carbon\Carbon;
use Illuminate\Support\Arr;

class VendorController extends Controller
{
    // To get all vendors
    public function vendors()
    {
        $vendors = Vendor::where('verified', 1)->paginate(10);
     
        $response = [
            'vendors' => $vendors,
        ];
        // $response->makeHidden(['account_number', 'account_name', 'bank_name', 'instagram', 'twitter', 'bio']);

        return response()->json($response);
    }

        public function get_offline_data(Request $request)
    {
        $items = Auth::user()->vendor()->first()->categories()
        ->with(['items.main_option'])->get();
        $response = [
            'items' => $items,
        ];
        return response()->json($response);
    }
    
    public function load(Request $request)
    {
        $vendor = Auth::user()->vendor()->with(['tags',  'area'])->withCount(['orders' => function ($query) {
                    $query->where('status', 4);
            }, 'reviews'])->get();
        $wallet = Auth::user()->vendor->orders()->where('status', 4)->sum('total');
        $vendor[0]['wallet'] = $wallet;
        $response = [
            'vendor' => $vendor
        ];
        return response()->json($response);
    }



    public function tags(Request $request)
    {
        $tags = Tag::select('tag as text', 'id as value', 'type as type')->get();
        // $rating_avg = Reviews::where('vendor_id', $request->id)->avg('rating');
        $response = [
            'tags' => $tags,
            // 'rating' => $rating_avg
        ];
        return response()->json($response);
    }
    public function upload(Request $request)
    {
        $files = $request->file('files');
        request()->validate([
            'files' => 'required',
            'files.*' => 'image|mimes:jpeg,png,JPG,jpg,gif,svg|max:4048'
        ]);
        $vendor = Auth::user()->vendor;
        foreach ($files as $file) {
            $image_name = $file->getRealPath();
            Cloudder::upload($image_name, null, array("width" => 400, "height" => 400, "crop" => "fit", "quality" => "auto", "fetch_format" => "auto"));
            $image_url = Cloudder::show(Cloudder::getPublicId(), ["width" => 400, "height" => 400]);
            $vendor->image = str_replace("http://", "https://", $image_url);
        }
        $vendor->save();
        $success['message'] = 'Image uploaded successfully';
        return response()->json(['success' => $success], 200);
    }
    public function find(Request $request)
    {
        $vendor = Vendor::where('user_id', $request->user_id)
            ->first();
        // $rating_avg = Reviews::where('vendor_id', $request->id)->avg('rating');
        $response = [
            'vendor' => $vendor,
            // 'rating' => $rating_avg
        ];
        return response()->json($response);
    }
    public function details(Request $request)
    {
        $vendor = Vendor::where('id', $request['id'])->first();
        $response = [
            'vendor' => $vendor,
            'address' => $vendor->address(),
            'location' => $vendor->location(),
            'specialty' => $vendor->specialty()
        ];
        return response()->json($response);
    }
    public function popular(Request $request)
    {

        $vendor = Vendor::where('id', $request['id'])
            ->first();
        $response = [
            'popular' => $vendor->orders,
            'location' => $vendor->location
        ];
        return response()->json($response);
    }
    public function summary(Request $request)
    {
     $now = Carbon::now();
     $today = Carbon::today();
        switch ($request->type) {
            case 1:
              $start = $today;
            break;
            case 2:
                $start = $now->startOfWeek();
            break;
            case 3:
                $start = $now->startOfMonth();
            break;
            case 4:
                $start = $now->startOfYear();
            break;
            default:
            $start = $today;
          }
          $det = array();
          switch ($request->category) {
            case 'sales':
                $data = Auth::user()->vendor->orders()->whereBetween('created_at', [$start, now()])
                ->where('status', 4)
                ->select('created_at', 'grand_total as value')
                ->orderBy('created_at')
                ->get();
                array_push($det, ['value' => $data->count() , 'title' => 'Delivered' ]);
                $data4 = Auth::user()->vendor->orders()->whereBetween('created_at', [$start, now()])
                ->where('status', 5)
                ->sum('grand_total');
                array_push($det, ['value' => $data4 , 'title' => 'Rejected' ]);
                $data5 = Auth::user()->vendor->orders()->whereBetween('created_at', [$start, now()])
                ->where('status', 3)
                ->sum('grand_total');
                array_push($det, ['value' => $data5 , 'title' => 'In-transit' ]);
                $data6 = Auth::user()->vendor->orders()->whereBetween('created_at', [$start, now()])
                ->where('status', 1)
                ->orWhere('status', 0)
                ->sum('grand_total');
                array_push($det, ['value' => $data6 , 'title' => 'Pending' ]);
            break;
            case 'orders':
                $data = Auth::user()->vendor->orders()->whereBetween('created_at', [$start, now()])
                ->where('status', 4)
                ->select('created_at')
                ->orderBy('created_at')
                ->get();
                array_push($det, ['value' => $data->count() , 'title' => 'Delivered' ]);
                $data4 = Auth::user()->vendor->orders()->whereBetween('created_at', [$start, now()])
                ->where('status', 5)
                ->count();
                array_push($det, ['value' => $data4 , 'title' => 'Rejected' ]);
                $data5 = Auth::user()->vendor->orders()->whereBetween('created_at', [$start, now()])
                ->where('status', 3)
                ->count();
                array_push($det, ['value' => $data5 , 'title' => 'In-transit' ]);
                $data6 = Auth::user()->vendor->orders()->whereBetween('created_at', [$start, now()])
                ->where('status', 1)
                ->orWhere('status', 0)
                ->count();
                array_push($det, ['value' => $data6 , 'title' => 'Pending' ]);
            break;
            case 'transactions':
                $data = Auth::user()->vendor->orders()->whereBetween('created_at', [$start, now()])
                ->where('status', 4)
                ->select('created_at', 'grand_total as value')
                ->orderBy('created_at')
                ->get();
            break;
            default:
            $data = Auth::user()->vendor->orders()->whereBetween('created_at', [$start, now()])
                ->where('status', 4)
                ->select('created_at', 'grand_total as value')
                ->orderBy('created_at')
                ->get();
          }
          switch ($request->type) {
              case 1:
                // $data2 = array();
              $data2 = $data->groupBy(function ($val) {
                    return Carbon::parse($val->created_at)->format('h:A');
                });
                // $keys = $data3->keys()->all();
                //  foreach ($keys as $d) {
                    // $data2 = $data3[7]->sum('value');
                    // $b = [
                    //     $data3[0] => $sum
                    // ];
                    // array_push($data2, $b);
                // }
            break;
            case 2:
                $data2 = $data->groupBy(function ($val) {
                    return Carbon::parse($val->created_at)->format('D');
                });
            break;
            case 3:
                $data2 = $data->groupBy(function ($val) {
                    return Carbon::parse($val->created_at)->weekOfMonth;
                });
            break;
            case 4:
                $data2 = $data->groupBy(function ($val) {
                    return Carbon::parse($val->created_at)->format('M');
                });
            break;
            default:
            $data2 = $today;
          }
        //   ->groupBy(function ($val) {
        //     return Carbon::parse($val->created_at)->hour;
        // })
        $list = array();
        switch ($request->category) {
            case 'sales':
                foreach ($data2 as $key => $value) {
                    array_push($list, ['value' => $value->sum('value'), 'title' => $key ]);
                } 
            break;
            case 'orders':
                foreach ($data2 as $key => $value) {
                    array_push($list, ['value' => $value->count('created_at'), 'title' => $key ]);
                }
            break;
            case 'transactions':
                foreach ($data2 as $key => $value) {
                    array_push($list, ['value' => $value->sum('value'), 'title' => $key ]);
                } 
            break;
            default:
            foreach ($data2 as $key => $value) {
                array_push($list, ['value' => $value->sum('value'), 'title' => $key ]);
            } 
          }
       
      
        $response = [
                'data'=> $list,
                'extra' => $det
            ];
        return response($response);
    }
    public function setfee(Request $request)
    {
        $vendor = Auth::user()->vendor;
        $areas = $vendor->area;
        $fee = $request->fee;
        
        foreach ($areas as $i=>$area) {
            $vendor->area()->updateExistingPivot($area->id, ['fee' => $fee[$i]]);
        }
            
        $response = [
                'message' => 'successful'
            ];
        return response()->json($response);
    }
    public function changeStatus(Request $request)
    {
        $user = Auth::user();
        $vendor = $user->vendor;
        $vendor->status = $request->status;
        $vendor->save();
        $response = [
                'message' => 'successful'
            ];
        return response()->json($response);
    }
    public function paySet(Request $request)
    {
        $user = Auth::user();
        $vendor = $user->vendor;
        $vendor->cash_on_delivery = $request->cash;
        $vendor->card_on_delivery = $request->card;
        $vendor->minimum_order = $request->minimum;
        $vendor->pos_charge = $request->pos_charge;
        $vendor->account_name = $request->account_name;
        $vendor->account_number = $request->account_number;
        $vendor->bank_name = $request->bank_name;
        $vendor->save();
        $response = [
                'message' => 'successful'
            ];
        return response()->json($response);
    }
    public function menu(Request $request)
    {
        $vendor = Vendor::find($request['id']);
        $response = [
            'menu' => $vendor->menu,
            'location' => $vendor->location,

        ];
        return response()->json($response);
    }

    public function save(Request $request)
    {

        $validator = $request->validate([
            'name' => 'required|string|max:255|unique:vendors',
            'user_id' => 'required'
        ]);
        if (!$validator) {
            return response(['errors' => $validator->errors()->all()], 422);
        }

        $user = Auth::user();
        $user->role = $request->category;
        $user->save();
        $vendor = new Vendor;
        $vendor->name =  $request->name;
        $vendor->phone = $request->phone;
        $vendor->type = $request->type;
        $vendor->bio = $request->bio;
        $vendor->address = $request->address;
        $vendor->city = $request->city;
        $vendor->lat = $request->lat;
        $vendor->status = false;
        $vendor->lng = $request->lng;
        $vendor->place_id = $request->place_id;
        $user->vendor()->save($vendor);
        $vendor->save();
        $tags = $request->tags;
        $areas = $request->areas;
        if ($areas) {
            $vendor->area()->sync($areas);
        }
        $vendor->tags()->attach($tags);
        $duration = $request->duration;
        $distance = $request->distance;

        foreach ($areas as $i => $area) {
            $vendor->area()->updateExistingPivot($area, ['distance' => $distance[$i], 'duration' => $duration[$i]]);
        }
        $response = [
            'message' => 'Registeration successful'
        ];
        return response()->json($response);
    }

    public function update(Request $request)
    {
      
            $vendor = Auth::user()->vendor;
            $vendor->name = $request->name;
            $vendor->bio = $request->bio;
            $vendor->phone = $request->phone;
            $vendor->save();
            if($request->areas){
                $areas = $request->areas;
                $vendor->area()->sync($areas);
            }
            if($request->tags){
                $tags = $request->tags;
                $vendor->tags()->sync($tags);
            }
            $duration = $request->duration;
            $distance = $request->distance;

            foreach ($areas as $i => $area) {
                $vendor->area()->updateExistingPivot($area, ['distance' => $distance[$i], 'duration' => $duration[$i]]);
            }
            return response([
                'status' => 'success',
            ], 200);
    }

    public function delete(Request $request)
    {
        if (Auth::user()->role === 'admin') {
            $vendor = Vendor::find($request->vendor_id);
            $vendor->delete();
        }
        return response([
            'status' => 'deleted',
            'data' => $vendor
        ], 200);
    }
    public function all()
    {
        $vendor = Auth::user()->vendor->categories;
        return response([
            'status' => 'success',
            'data' => $vendor
        ], 200);
    }
    public function ordered()
    {
        $vendor1 = Auth::user()->vendor->orders()->where('status', 4)->select('id')->with(['items' => function ($query) {
            $query->select('item_id', 'order_id');
            }, 'options' => function ($query) {
            $query->select('option_id', 'order_id');
            }])->get();
        // $vendor2 = Auth::user()->vendor->option_order;
        return response([
            'status' => 'success',
            'data1' => $vendor1
            // 'data2' => $vendor2,
        ], 200);
    }
}
