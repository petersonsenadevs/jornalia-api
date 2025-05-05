<?php

namespace App\Providers;

use App\Services\Redis\RedisService;
use App\Services\Salary\SalaryService;
use App\Services\Salary\SalaryServiceInterface;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Client\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        $this->app->bind(SalaryServiceInterface::class, SalaryService::class);
        
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            return Limit::perMinute(60)->by($request->user()?->id ?: $request->ip());
        });
    }
}
