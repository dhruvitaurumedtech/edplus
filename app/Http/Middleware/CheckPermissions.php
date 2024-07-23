<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ApiTrait;

class CheckPermissions
{
    use ApiTrait;
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle($request, Closure $next, $featureId, $actionId)
    {
      
        $user = Auth::user();
        $cacheKey = "user_permissions_{$user->id}";
        if (Cache::has($cacheKey)) {
            $modules = Cache::get($cacheKey);
            foreach ($modules as $module) {
                foreach ($module->Features as $feature) {
                    if ($feature->id == $featureId) {
                        foreach ($feature->actions as $action) {
                            if ($action['id'] == $actionId && $action['has_permission']) {
                                return $next($request);
                            }
                        }
                    }
                }
            }
        }
        // return $next($request);
        return $this->response([], "Permission denied", false, 403);
    }
}
