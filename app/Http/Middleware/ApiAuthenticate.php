<?php 

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class ApiAuthenticate
{
    public function handle($request, Closure $next, $guard = 'api')
    {
        if (Auth::guard($guard)->guest()) {
            return response()->json([
                'data' => [],
                'message' => 'Unauthorized!',
                'success' => false,
            ], 401);
        }

        return $next($request);
    }
}
?>