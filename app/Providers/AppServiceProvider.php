<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Blade;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        Blade::directive('canButton', function ($expression) {
            list($permission, $menuName) = explode(',', trim($expression, '()'));
    
            return "<?php if(auth()->check() && auth()->user()->canButton($permission, $menuName)): ?>";
        });
    
        Blade::directive('endCanButton', function () {
            return '<?php endif; ?>';
        });
    }
}
