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

    
}

?>