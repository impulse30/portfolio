<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{

 public function register(Request $request){
     $validated = Validator::make($request->all(),[
         'name'=>'required|string',
         'email'=>'required|email',
         'password'=>'required|confirmed'

     ]);
     if($validated->fails()){
         return response()->json($validated->errors());
     }
     $user = User::where('email',$request->email)->first();
     if($user){
         return response()->json(['error'=>'Email already exists']);
     }
     try {
         $user =User::create([
             'name'=>$request->name,
             'email'=>$request->email,
             'password'=>Hash::make($request->password)

         ]);
         $token = $user->createToken('token')->plainTextToken;
         $user['token']=$token;
         return response()->json(['message'=>'User created successfully','data'=>$user]);

     }catch (\Exception $exception){
         return response()->json(['error'=>$exception->getMessage()]);
     }
 }
 public function login(Request $request){
     $validated = Validator::make($request->all(),[
         'email'=>'required|email',
         'password'=>'required'

     ]);

     if($validated->fails()){
         return response()->json($validated->errors());
     }
     $credentials =$request->only('email','password');
     if(!auth()->attempt($credentials)){
         return response()->json(['error'=>'Email or Password not matched']);
     }
     try {
         $user =User::where('email',$request->email)->first();
         $token=$user->createToken('token')->plainTextToken;
         $user['token']=$token;
         return response()->json(['message'=>'User login successfully','data'=>$user]);

     }catch (\Exception $exception){
         return response()->json(['error'=>$exception->getMessage()]);
     }

 }

}
