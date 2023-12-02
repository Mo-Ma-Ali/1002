<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class checkIfWarehouse
{
    /**
     * Handle an incoming request.
     *iswarehouse
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->header('Authorization');
        $user = User::where('api_token', $token)->first();
        $warehouse=$user->iswarehouse;
        //dd($warehouse);
        //Check if the user is not a warehouse
        if($warehouse==false)
        { return response()->json(['error' => 'Access denied'], 401);}
        
        return $next($request);
    }
}
