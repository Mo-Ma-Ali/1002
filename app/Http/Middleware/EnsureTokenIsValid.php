<?php

namespace App\Http\Middleware;

use App\Models\User;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureTokenIsValid
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next)
    {
        // Get the token from the request headers
        $token = $request->header('Authorization');

        if (!$token) {
            return response()->json(['error' => 'Token not provided'], 401);
        }

        // Check if the token exists in the database
        $user = User::where('api_token', $token)->first();
       // dd($user);
        if (!$user) {
            return response()->json(['error' => 'Invalid token'], 401);
        }

        // Attach the user to the request for later use if needed
        $request->user = $user;

        // Continue with the request
        return $next($request);
    }
}
