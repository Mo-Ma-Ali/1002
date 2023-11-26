<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Exceptions;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|unique:users',
            'phone' => 'required|string|unique:users',
            'password' => 'required|string|min:8',
            'ispharmacy' => 'sometimes|boolean',
            'iswarehouse' => 'sometimes|boolean',
        ]);
    
        $request->merge(['password' => Hash::make($request->password)]);
       
        if ($request->has('ispharmacy')) {
            $request->merge(['iswarehouse' => !$request->input('ispharmacy')]);
        } elseif ($request->has('iswarehouse')) {
            $request->merge(['ispharmacy' => !$request->input('iswarehouse')]);
        }

        $user = User::create($request->all());
        
        $token = Str::random(60);
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user,
            'token'=>$token
        ], 201);
    }





    public function login(Request $request)
{
   $phone=$request->input('phone');
    $password=$request->input('password');
    $name=User::where('name',$request->input('name'))->first()->name;
    $dphone= User::where('phone',$request->input('phone'))->first()->phone;
$dpassword=  User::where('phone', $request->input('phone'))->first()->password;


$token = Str::random(60);
if ( password_verify($password,$dpassword)) {
    return response()->json([
        'message' => 'User logged in successfully',
        'token'=>$token
    ], 200);
} else {
    return response()->json([
        'Error' => 'phone number or the password is Incorrect'
    ], 401);
        }
}



public function logout(Request $request)
{
    $phone=$request->input('phone');
    $dphone= User::where('phone',$phone)->first();
    if(!$dphone)
    return response()->json(['messege'=>'user not fund'],404);

    return response()->json(['messege'=>'user logged out seccessfuly'],200);
}

}
