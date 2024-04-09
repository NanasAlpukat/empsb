<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Validator;

class userController extends Controller
{
    public function user(){
        $user = Auth::user();
        return response()->json([
            'data' => $user
        ]);
    }

    public function login(Request $request){
        $validate =  Validator::make($request->all(),[
            'email' => ['required','email'],
            'password' => ['required']
        ],[
            'required' => 'Inputan ini wajib isi',
            'email' => 'Inputan ini wajib email',
        ]);

        if($validate->fails()){
            return response()->json($validate->errors(),422);
        }

        try{
            if(!Auth::attempt($request->only('email','password'))){
                return response()->json([
                    'message' => 'Password atau email anda salah'
                ],202);
            }
            $user = Auth::user();
            $token = $user->createToken('token-name', ['server:update'])->plainTextToken;
            return response()->json([
                'message' => 'Berhasil login',
                'user' => $user,
                'token' => $token,
                
            ],200);
           

        }catch(QueryException $e){
            return response()->json([
                'message' => $e->errorInfo
            ],500);
        }
    }

    public function logout(){
        
        try{
            Auth::user()->currentAccessToken()->delete();
            // Session::flush();
            return response()->json([
                'message' => 'Berhasil logout',
            ],200);
        }catch(QueryException $e){
            return response()->json([
                'message' => $e->errorInfo
            ],500);
        }
}

public function setPass(Request $request){
    $validate =  Validator::make($request->all(),[
        'email' => ['required','email'],
        'old_password' => ['required'],
        'new_password' => ['required'],
        'confirm_password' => ['required'],
    ],[
        'required' => 'Inputan ini wajib isi',
        'email' => 'Inputan ini wajib email',
    ]);

    if($validate->fails()){
        return response()->json($validate->errors(),422);
    }
    

    try{
    $user = user::where('email',$request->email)->first();
    if(isset($user)){
        if(Hash::check($request->old_password,$user->password)){
            if($request->new_password == $request->confirm_password){
                $user->update([
                    'password' => Hash::make($request->new_password)
                ]);

                return response()->json([
                    'message' => 'Password berhasil di ubah'
                ],200);
            }
        }
    }
    return response()->json([
        'message' => 'Mohon maaf email tidak terdaftar'
    ],422);
}catch(QueryException $e){
    return response()->json([
        'message' => $e->errorInfo
    ],500);
}

}
}
