<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

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



        //else
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
                return ['code'=>1, 'data'=>$result, 'msg'=>'User information updated successfully'];

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

}
