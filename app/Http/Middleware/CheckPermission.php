<?php

namespace App\Http\Middleware;

use App\Models\Menu;
use App\Models\permission;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next,$menu_name): Response
    {
        // if()
        $menu = Menu::where('menu_name', $menu_name)->first();
        
        // Check if the menu exists
        if ($menu) {
            $permission = Permission::where('role_id', Auth::user()->role_type)
                ->where('menu_id', $menu->id)
                ->first();
        
            // Check if the user has 'add' permission for the menu
            if ($permission && $permission->add == 1) {
                return $next($request);
            }
        }
        
        // If the user doesn't have the required permission or the menu doesn't exist, you can redirect or return an error response
        return redirect()->back()->with('error', 'Unauthorized access');
    }
}
