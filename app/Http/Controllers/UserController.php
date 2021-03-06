<?php
namespace App\Http\Controllers;
use Illuminate\Http\Request; 
use App\Http\Controllers\Controller; 
use App\User; 
use Illuminate\Support\Facades\Auth; 
use Validator;
use JD\Cloudder\Facades\Cloudder;
use AfricasTalking\SDK\AfricasTalking;
use App\Favourites;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller 
{
public $successStatus = 200;

    public function resetpassword(Request $request){
        $user = User::where($request->type, $request->data)->first();
        if ($request->type === 'phone') {    
    $validator = Validator::make($request->all(), [
        'data' => 'required',
        'password' => 'required|string|min:6|confirmed',
        ]);
        
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else{
            $user = User::where('phone', $request->data)->first();
            $user->password = Hash::make($request->password);
            $user->save();
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['user'] =  $user;
            return response()->json(['success' => $success], $this->successStatus);  
        }
    } 
      else if ($request->type === 'email') {    
    $validator = Validator::make($request->all(), [
        'data' => 'required',
        'password' => 'required|string|min:6|confirmed',
        ]);
        
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else{
            $user = User::where('email', $request->data)->first();
            $user->password = Hash::make($request->password);
            $user->save();
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['user'] =  $user;
            return response()->json(['success' => $success], $this->successStatus);  
        }
    } else {
            return response()->json(['error' => 'Invalid request.'], 401);
        } 
    }
    public function setpassword(Request $request){ 
        
    $validator = Validator::make($request->all(), [
        'phone' => 'required',
        'password' => 'required|string|min:6|confirmed',
        ]);
        
        if ($validator->fails()) {
            return response(['errors' => $validator->errors()->all()], 422);
        } else{
            $user = User::where('phone', $request->phone)->first();
            $user->password = Hash::make($request->password);
            $user->save();
            $success['token'] =  $user->createToken('MyApp')->accessToken;
            $success['user'] =  $user;
            return response()->json(['success' => $success], $this->successStatus);  
        }
    }
    public function load(){
        $user = Auth::user();
        $success['user'] =  $user;
        return response()->json(['success' => $success], $this->successStatus);
    }
    public function setfcm(Request $request){
        $user = Auth::user();
        if(isset($request->type)){
            switch ($request->type) {
                case 'vendor':
                    # code...
                    $vendor = $user->vendor;
                    $vendor->token = $request->token;
                    $vendor->save();
                break;
                case 'delivery':
                    # code...
                    $delivery_agent = $user->delivery_agent;
                    $delivery_agent->token = $request->token;
                    $delivery_agent->save();
                break;
                default:
                    # code...
                    break;
            }
        } else{
           $user->token = $request->token;
        }
        $user->save();
        return response()->json(['success' => 'success'], $this->successStatus);
    }
    public function login(){ 
        if(Auth::attempt(['phone' => request('phone'), 'password' => request('password')])){ 
            $user = Auth::user(); 
            $success['token'] =  $user->createToken('MyApp')->accessToken; 
            $success['user'] =  $user; 
            return response()->json(['success' => $success], $this->successStatus); 
        }
        else{
            return response()->json(['error'=>'Invalid phone number or password.'], 400); 
        } 
    }

    public function register(Request $request){
        $digits = 5;
        $rand_code = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
        $user =  new User;
        $user->verification_code = $rand_code;
        $choice = $request->choice;
        if ($choice === 'true') {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'email', 'unique:users'],
            ]);
            if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 422);            
            } else {
                $to_name = 'No reply';
                $to_email = $request->email;
                $data = array('pin' => $rand_code);
                $result = Mail::send('emails.mail', $data, function ($message) use ($to_name, $to_email) {
                    $message->to($to_email, $to_name)->subject('Verify your Email');
                    $message->from('admin@greatdixers.xyz', 'Food Republic');
                });
                $user->email = $to_email;
                $user->verification_type = 'email';
                $user->save();
                $favourites = new Favourites;
                $user->favourites()->save($favourites);
                $success['user'] = ['email' => $user->email, 'type' => 'email'];
                $success['result'] =  $result;
                return response()->json(['success' => $success], $this->successStatus); 
            }
        } else if ($choice === 'false') {
            $validator = Validator::make($request->all(), [
                'phone' => ['required', 'string', 'unique:users'],
                ]);
                $FoundUser = User::where('phone', $request->phone)->first();
            if ($validator->fails()) {
                if (isset($FoundUser->state_id)) {
                    return response()->json(['error'=>$validator->errors()], 422);
                }else{
                   return $this->sendCode($request->phone, $FoundUser, $rand_code);
                }
            } else {
                $user = new User;
               return $this->sendCode($request->phone, $user
               , $rand_code);
            }
        } else {
            return 'an error occured';
        }
}
public function sendCode($userPhone, $user, $rand_code){
    $phone = '+234'.substr($userPhone, 1); 
    $username = 'bona23'; // use 'sandbox' for development in the test environment
    $apiKey   = env('AFRIKASTLKN_KEY'); // use your sandbox app API key for development in the test environment
    $AT       = new AfricasTalking($username, $apiKey);
    // Get one of the services
    $sms      = $AT->sms();
    // Use the service
    $result = $sms->send([
        'to'      => [$phone],
        // 'from'      => 'edeyapp',
        'message' => "Your Food Repulic Passcode is {$rand_code}"
    ]);
    if ($result['data']->SMSMessageData->Recipients[0]->statusCode === 101) {
        $user->phone = $userPhone;
        $user->verification_type = 'phone';
        $user->verification_code = $rand_code;
        $user->save();
        $favourites = new Favourites;
        $user->favourites()->save($favourites);
        $success['user'] = ['phone' => $user->phone, 'type' => 'phone'];
        return response()->json(['success'=>$success], $this-> successStatus); 
    } else {
        $success['error'] =  $result['data']->SMSMessageData->Recipients[0]->status;
        return response()->json(['success'=>$success], $this-> successStatus); 
         
     }
}
    public function reset(Request $request){
        $digits = 5;
        $rand_code = rand(pow(10, $digits - 1), pow(10, $digits) - 1);
        $user = User::where($request->type, $request->data)->first();
        $user->verification_code = $rand_code;
        if ($request->type === 'email') {
            $validator = Validator::make($request->all(), [
                'email' => ['required', 'email'],
            ]);
            if ($validator->fails()) {
            return response()->json(['error'=>$validator->errors()], 422);            
            } else {
                $to_name = 'No reply';
                $to_email = $request->email;
                $data = array('pin' => $rand_code);
                $result = Mail::send('emails.mail', $data, function ($message) use ($to_name, $to_email) {
                    $message->to($to_email, $to_name)->subject('Verify your Email');
                    $message->from('admin@greatdixers.xyz', 'Food Republic');
                });
                $user->email = $request->email;
                $user->verification_type = 'email';
                $user->save();
                $success['user'] = ['email' => $user->email, 'type' => 'email'];
                $success['result'] =  $result;
                return response()->json(['success' => $success], $this->successStatus); 
            }
        } else if ($request->type === 'phone') {
            $validator = Validator::make($request->all(), [
                'phone' => ['required', 'string', 'min:11', 'max:11'],
                ]);
            if ($validator->fails()) {
                return response()->json(['error'=>$validator->errors()], 422);            
            } else {
                $phone = '+234'.substr($request->phone, 1); 
                $username = 'bona23'; // use 'sandbox' for development in the test environment
                $apiKey   = env('AFRIKASTLKN_KEY'); // use your sandbox app API key for development in the test environment
                $AT       = new AfricasTalking($username, $apiKey);
                // Get one of the services
                $sms      = $AT->sms();
                // Use the service
                $result = $sms->send([
                    'to'      => [$phone],
                    // 'from'      => 'emekasulk',
                    'message' => "Your Food Repulic Passcode is {$rand_code}"
                ]);
                $user->phone = $request->phone;
                $user->verification_type = 'phone';
                $user->save();
                $success['user'] = ['phone' => $user->phone, 'type' => 'phone'];
                $success['result'] =  $result;
                return response()->json(['success'=>$success], $this-> successStatus); 
            }
        } else {
            return 'an error occured';
        }
   }
   
   public function load_favourites(){
        $user = Auth::user();
        $success['favourites'] = $user->favourites()
        ->with(['vendors' => function ($query) {
            $query->select('favourites_id', 'image', 'name');
        }])->get();
        return response()->json(['success' => $success], $this->successStatus);
     
   }
   public function favourite(Request $request){
      
    $user = Auth::user();
        $validator = Validator::make($request->all(), [
            'id' => 'required'
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        } else {
            $favourites = $user->favourites;
            $favourites->vendors()->toggle($request->id);
            $success['message'] = 'successfully added to favourites';
            return response()->json(['success' => $success], $this->successStatus);
    }
    }
   public function remove_favourite(Request $request){
      
    $user = Auth::user();
     if ($user->favourites) {
            $user->favourites->vendors()->detach($request->id);
       }
        $success['message'] = 'successfully removed from favourites';
        return response()->json(['success' => $success], $this->successStatus);
    
    }
    public function registerdata(Request $request){
        $validator= '';
        if ($request->type === 'email') {
            $validator = Validator::make($request->all(), [
            'phone' => 'required|string|unique:users|min:11|max:11',
            'first_name' => 'required|string',
            'surname' => 'required|string',
            'email' => 'required|email',
        ]);
        }else{
            $validator = Validator::make($request->all(), [
                'phone' => 'required|string|min:11|max:11',
                'first_name' => 'required|string',
                'surname' => 'required|string',
                'email' => 'required|email|unique:users',
            ]);
        }
 
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        } else {
        $user = User::where($request->type, $request->data)->first();
        $user->first_name = $request->first_name;
        $user->middle_name = $request->middle_name;
        $user->surname = $request->surname;
        $user->email = $request->email;
        $user->token = $request->token;
        $user->phone = $request->phone;
        $user->save();
        $success['user'] = $user;
        return response()->json(['success' => $success], $this->successStatus); 
        }   
    }
    public function passcode(Request $request){
        $validator = Validator::make($request->all(), [
            'passcode' => ['required'],
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        } else{
            $user = User::where($request->type, $request->data)->first();
            $d = $user->verification_code;
            $user->password = null;
            if ($d === $request->passcode) {
                $user->verification_code = null;
                $user->save();
                $success['user'] = $user;
                return response()->json(['success' => $success], $this->successStatus); 
            } else {
                $success['error'] = 'The entered passcode does not match';
                return response()->json(['success' => $success], $this->successStatus); 
            }
        }
    }
    public function upload(Request $request)
    {
        $files = $request->file('files');
        request()->validate([
            'files' => 'required',
            'files.*' => 'image|mimes:jpeg,JPG,png,jpg,gif,svg|max:4048'
        ]);
   
            $user = Auth::user();
            foreach ($files as $file) {
            $image_name = $file->getRealPath();
            Cloudder::upload($image_name, null, array("width" => 600, "height" => 600, "crop" => "fit", "quality" => "auto", "fetch_format" => "auto", "radius" => "max"));
            $image_url = Cloudder::show(Cloudder::getPublicId(), ["width" => 400, "height" => 400]);
            // $image_url = Cloudder::getPublicId();
            $user->image = str_replace("http://", "https://", $image_url);
            $user->save();
            // $file->storeAs('uploads', $file->getClientOriginalName());
        }
        $success['user'] = $user;
        return response()->json(['success' => $success], $this->successStatus); 
    }
    public function details() 
    { 
        $user = Auth::user(); 
        return response()->json(['success' => $user], $this-> successStatus); 
    } 
    public function edituser(Request $request) 
    {
        $validator = Validator::make($request->all(), [
            'phone' => 'required|min:11|max:11',
            'first_name' => 'required|string',
            'surname' => 'required|string',
            'email' => 'required|email',
        ]);
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        } else {
        $user = Auth::user(); 
        if ($user) {
            # code...
            $user->first_name = $request->first_name;
            $user->middle_name = $request->middle_name;
            $user->surname = $request->surname;
            $user->phone = $request->phone;
            $user->email = $request->email;
            $user->token = $request->token;
            $user->save();
            $success['user'] = $user;
            return response()->json(['success' => $success], $this->successStatus); 
            
        } else {
                $success['error'] = 'an error occured';
                return response()->json(['success' => $success], $this->successStatus);
            }
    }
    } 
    public function logout(){
        $user = Auth::user()->token();
        $user->revoke();
        return response()->json(['message' => 'successfully logged out'], $this->successStatus); 
    }

    public function orderall(Request $request)
    {
        $order = Auth::user()->orders()->select('id', 'tracking_id', 'grand_total', 'status', 'user_status')->latest()->paginate(20);
        $response = [
            'orders' => $order
        ];
        return response()->json($response);
    }
    public function orderpaid(Request $request)
    {
        $order = Auth::user()->orders()->find($request->id);
        $order->paid = true;
        $order->save();
        $response = [
            'message' => 'marked paid successful'
        ];
        return response()->json($response);
    }
    public function orderfind(Request $request)
    {
        $order = Auth::user()->orders()->with(['user' => function ($query){
            $query->select('id', 'phone');
        }, 'items' => function($query){
                $query->select('item_id','order_id', 'price', 'name', 'image');
        }, 'vendor' => function($query){
            $query->select('name', 'type', 'id');
    }, 'options', 'delivery' => function ($query) {
        $query->select('id', 'phone', 'name', 'image');
}, 'address.area', 'reviews'])->find($request->id);

        $response = [
            'order' => $order
        ];
        return response()->json($response);
    }
    public function orderread(Request $request)
    {
        $order = Auth::user()->orders()->find($request->id);
        $order->user_status = 1;
        $order->save();
        $order1 = $order->with(['user' => function ($query){
            $query->select('id', 'phone');
        }, 'items' => function($query){
                $query->select('item_id','order_id', 'price', 'name', 'image');
        }, 'vendor' => function($query){
            $query->select('name', 'type', 'id');
    }, 'options', 'delivery' => function ($query) {
        $query->select('id', 'phone', 'name', 'image');
}, 'address.area', 'reviews'])->find($request->id);
        $response = [
            'order' => $order1
        ];
        return response()->json($response);
    }


}