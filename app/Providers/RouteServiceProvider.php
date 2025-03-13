<?php

namespace App\Providers;

use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Foundation\Support\Providers\RouteServiceProvider as ServiceProvider;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\App;

class RouteServiceProvider extends ServiceProvider
{
    /**
     * The path to the "home" route for your application.
     *
     * This is used by Laravel authentication to redirect users after login.
     *
     * @var string
     */
    public const HOME = '/spa/auth-user';

    protected const PREFIX_API_DEFAULT = 'api_default_';
    protected const PREFIX_API_PUBLIC = 'api_public_';
    protected const PREFIX_API_CREW = 'api_crew_';
    protected const PREFIX_API_JAMAAH = 'api_participant_';

    /**
     * Define your route model bindings, pattern filters, etc.
     *
     * @return void
     */
    public function boot()
    {
        $this->configureRateLimiting();
        
        if (App::environment('production') && request()->getHost() == config('app.prodApiHost')) {
             $this->routes(function () {
                 Route::prefix('/')->middleware('api')->group(base_path('routes/api.php'));
             });
             
             return;
        }

        $this->routes(function () {
            Route::prefix('api')
                ->middleware('api')
                ->group(base_path('routes/api.php'));

            Route::middleware('web')
                ->group(base_path('routes/web.php'));
        });
    }

    /**
     * Configure the rate limiters for the application.
     *
     * @return void
     */
    protected function configureRateLimiting()
    {
        RateLimiter::for('api', function (Request $request) {
            if ($request->is('api/public/*')) {
                return self::generateLimitPublic($request);
            }

            $prefix = self::getPrefixRoute($request);
            $identifier = $request->user()?->id ?: $request->ip();

            return Limit::perMinute(60)->by($prefix . $identifier);
        });
    }

    protected static function generateLimitPublic(Request $request)
    {
        $prefix = self::PREFIX_API_PUBLIC . $request->route()->getName();
        $identifier = $request->user()?->id ?: $request->ip();
        return Limit::perMinute(60)->by($prefix . $identifier);
    }

    protected static function getPrefixRoute(Request $request)
    {
        $prefix = self::PREFIX_API_DEFAULT;

        if ($request->is('api/participant/*')) {
            $prefix = self::PREFIX_API_JAMAAH;
        }

        if ($request->is('api/crew/*')) {
            $prefix = self::PREFIX_API_CREW;
        }

        return $prefix;
    }
}
