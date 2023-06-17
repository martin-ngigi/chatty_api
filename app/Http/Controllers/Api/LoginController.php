<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Kreait\Firebase\Contract\Messaging;
use Kreait\Firebase\Messaging\CloudMessage;

class LoginController extends Controller
{
    //

    public function login(Request $request){ //
        // //validate data
        $validator = Validator::make($request->all(), [
            'avatar'=>'required',
            'name'=>'required | max:50',
            'type'=>'required',
            'open_id'=>'required',
            'email'=>' max:50',
            'phone'=>'max:20',
        ]);

        if($validator->fails()){
            return ['code'=>-1, "data"=>"no valid data", 'msg'=>$validator->errors()->first()];
        }
        // else{
        //     return ['code'=>1, "data"=>" valid data", 'msg'=>'data present'];

        // }

        try{
            $validated = $validator->validate();
            $map = [];
            $map['type']=$validated['type'];
            $map['open_id']=$validated['open_id'];

            $result = DB::table('users')->select('avatar',
                'name',
                'description',
                'type',
                'token',
                'access_token',
                'online',
                )
                ->where($map)->first();

                if(empty($result)){
                    $validated['token'] = md5(uniqid().rand(10000, 99999));
                    $validated['created_at'] = Carbon::now();
                    $validated['access_token'] = md5(uniqid().rand(1000000, 9999999));
                    $validated['expire_date'] = Carbon::now()->addDays(30);
                    $user_id=DB::table('users')->insertGetId($validated); //inserrt and get the inserted id
                    $user_result =DB::table('users')->select('avatar',
                    'name',
                    'description',
                    'type',
                    'token',
                    'access_token',
                    'online',
                    )->where('id', '=', $user_id)->first();
                    return ['code'=>0, 'data'=>$user_result, 'msg'=>'User has been created successfully'];
                }
                else{
                    $access_token = md5(uniqid().rand(1000000, 9999999));
                    $expire_date = Carbon::now()->addDays(30);
                    DB::table('users')->where($map)->update(
                        [
                            "access_token" => $access_token,
                            "expire_date" => $expire_date
                        ]
                    );
                    $result->access_token= $access_token;
                    return ['code'=>0, 'data'=>$result, 'msg'=>'User information updated successfully'];

                }
        }
        catch(\Exception $e){
            return ['code'=>-1, 'data'=>"No data available", 'msg'=>(string)$e];

        }

    }

    public function login2(){
        // return [
        //     "code"=>0,
        //     "data"=>"we alot of data",
        //     "msg"=>"Welcome you"
        // ];
        return ['code'=>1, "data"=>" valid data -----", 'msg'=>'data present'];
    }


    public function contact(Request $request){
        $token =$request->user_token;
        $res = DB::table("users")->select(
            'avatar',
            'description',
            'online',
            'token',
            'name'
        )->where("token", '!=', $token)->get();
        //)->get();
        return ['code'=>0, "data"=>$res, 'msg'=>'got all users info'];

    }

    public function send_notice(Request $request){

        // caller information
        $user_token = $request->user_token;
        $user_avatar = $request->user_avatar;
        $user_name = $request->user_name;

        // callee information
        $to_token = $request->to_token;
        $to_avatar = $request->to_avatar;
        $to_name = $request->to_name;
        $call_type = $request->call_type;
        $doc_id = $request->doc_id;

        if(empty($doc_id)){
            // if email, means that user calling for the first time.
            $doc_id = "";
        }

        ////1. voice 2. video 3. text, 4.cancel

        // get the other user information.
        $res = DB::table('users')
                    ->select("avatar", "name", "token", "fcm_token")
                    ->where("token", "=", $to_token)->first();
        
        /// if User doesn't exist.
        if(empty($res)){
            return[
                "code" => -1,
                "data"=>"",
                "msg"=>"Error: User doesn't exist."
            ];
        }

        $device_token = $res->fcm_token;
        try{
            // if device token is not empty.
            if(!empty($device_token)){
                $messaging = app("firebase.messaging"); /// initialize firebase configuration.
                
                if($call_type == "cancel"){
                    // if the user stated a call then ended the call.

                    $message = CloudMessage::fromArray([
                        "token" => $device_token, //optional
                        /// information sent to the other end user.
                        "data" =>[
                            "token" => $user_token,
                            "avatar" => $user_avatar,
                            "name" => $user_name,
                            "doc_id" => $doc_id,
                            "call_type" => $call_type
                        ]
                    ]);

                    //send the message.
                    $messaging->send($message);

                    return[
                        "code" => 0,
                        "data"=>$to_token,
                        "msg"=>"success"
                    ]; 
                }
                else if($call_type=="voice"){
                 
                    /***
                    NB: When sending notifications to android devices, it happes through firebase directly.
                    But when sending notifications to ios devices, It starts from device, to firebase, to Apple Push Notification service (APNs)  
                    */
           
                    $message = CloudMessage::fromArray([
                    'token' => $device_token, // optional
                    'data' => [
                        'token' => $user_token,
                        'avatar' => $user_avatar,
                        'name' => $user_name,
                        'doc_id' => $doc_id,
                        'call_type' => $call_type,
                    ],
                    'android' => [
                        "priority" => "high",
                        "notification" => [
                            "channel_id"=> "com.dbestech.chatty.call",
                            'title' => "Voice call made by ".$user_name,
                            'body' => "Please click to answer the voice call",
                            ]
                        ],
                        'apns' => [
                        // https://firebase.google.com/docs/reference/fcm/rest/v1/projects.messages#apnsconfig
                        'headers' => [
                            'apns-priority' => '10',
                        ],
                        'payload' => [
                            'aps' => [
                                'alert' => [
                                   'title' => "Voice call made by ".$user_name,
                                   'body' => "Please click to answer the video call",
                                ],
                                'mutable-content'=>1, 
                                'content-available'=>1,
                                'badge' => 1,
                                'sound' =>'task_cancel.caf'
                            ],
                        ],
                    ],
                    ]);
                    
                   $messaging->send($message);

                   return[
                        "code" => 0,
                        "data"=>$to_token,
                        "msg"=>"success"
                    ];        
                
                }
            }
            else{
                return[
                    "code" => -1,
                    "data"=>"",
                    "msg"=>"Error: Device token is empty."
                ];
            }

        }
        catch(\Exception $e){
            return[
                "code" => -1,
                "data"=>"",
                "msg"=>"Error: ".(string)$e
            ];
        }
    }

    public function bind_fcmtoken(Request $request){
        $token = $request->user_token;
        $fcm_token = $request->fcm_token;
        if(empty($fcm_token)){
            return[
                "code" => -1,
                "data"=>"",
                "msg"=>"Error getting token. FCM token is empty."
            ];
        }

        /// update database
        DB::table('users')
            ->where('token', '=', $token)
            ->update(['fcm_token' => $fcm_token]);

        return[
            // "code" => -1,
            "code" => 0,
            "data"=>$fcm_token,
            "msg"=>"Updated fcm_token successfully"
        ];
    }

}
