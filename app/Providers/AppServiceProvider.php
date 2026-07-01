<?php

namespace App\Providers;

use App\Services\BankService;
use App\Services\BankValidationManager;
use App\Services\BankValidators\DummyValidator;
use App\Services\BankValidators\FlipValidator;
use App\Services\BankValidators\XenditValidator;
use Illuminate\Cache\RateLimiting\Limit;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\RateLimiter;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(BankService::class);

        $this->app->singleton(BankValidationManager::class, function ($app) {
            $manager = new BankValidationManager;

            $manager->addValidator(new XenditValidator);
            $manager->addValidator(new FlipValidator);
            $manager->addValidator(new DummyValidator);

            return $manager;
        });
    }

    public function boot(): void
    {
        RateLimiter::for('api', function (Request $request) {
            $key = $request->user()?->id ?: $request->ip();

            return Limit::perDay(5000)->by($key);
        });
    }
}

