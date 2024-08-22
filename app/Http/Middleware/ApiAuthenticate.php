<?php 

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ApiAuthenticate 
{
    public function handle($request, Closure $next, $guard = 'api')
    {
        // Check if the user is authenticated using the specified guard
        if (Auth::guard($guard)->guest()) {
            return response()->json([
                'data' => [],
                'message' => 'Unauthorized!',
                'success' => false,
            ], 401);
        }

        // If authenticated, pass the request to the next middleware or controller
        return $next($request);
    }
}
