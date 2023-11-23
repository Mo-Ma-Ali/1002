<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\DB;
class UserController extends Controller
{
    public function store(Request $request)
    {
        // Validate the request data
        $request->validate([
            'name' => 'required|string|max:255',
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
    
        return response()->json([
            'message' => 'User created successfully',
            'user' => $user
        ], 201);
    }
    public function login(Request $request)
{
       
    $password=$request->input('password');

$dpassword=  User::where('phone', $request->input('phone'))->first()->password;

if ( password_verify($password,$dpassword)) {
    return response()->json([
        'message' => 'User logged in successfully'
    ], 201);
} else {
    return response()->json([
        'Error' => 'User does not found'
    ], 404);
        }
}
    }
