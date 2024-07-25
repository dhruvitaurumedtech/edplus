<?php
namespace App\Http\Middleware;

use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Illuminate\Http\Request;

class Authenticate extends Middleware
{
    protected function redirectTo(Request $request): ?string
    {
        if ($request->expectsJson()) {
            return null;
        }
        if ($request->is('admin/*')) {
            return route('admin.login'); // Adjust this route as per your application
        }
         return route('login');
    }

    protected function unauthenticated($request, array $guards)
    {
        if ($request->expectsJson()) {
            abort(response()->json(
                [
                    'data' => [],
                    'message' => 'Unauthorized!',
                    'success' => false
                ],
                401
            ));
        }
       parent::unauthenticated($request, $guards);
    }
}

?>