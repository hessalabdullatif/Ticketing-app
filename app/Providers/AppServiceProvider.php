<?php

namespace App\Providers;
use App\Services\PaymentGateways\FakeMada;
use App\Services\PaymentGateways\FakeVisa;
use App\Services\PaymentGateways\PaymentGateway;
use Carbon\CarbonImmutable;
use Illuminate\Support\Facades\Date;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\ServiceProvider;
use Illuminate\Validation\Rules\Password;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //this is an instruction to Laravel meaning: "from now on, every time any code in the project asks for a PaymentGateway, don't leave it unanswered — 
        // run this function and hand back the result." This registration is called a Binding
        $this->app->bind(PaymentGateway::class, function ($app) {
            return match (config('services.payment_gateway')) {
                'visa' => new FakeVisa(),
                'mada' => new FakeMada(),
                default => new FakeMada(),
            };
        });
   }
    /**
     * Register any application services.
     */
   
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        $this->configureDefaults();
    }

    /**
     * Configure default behaviors for production-ready applications.
     */
    protected function configureDefaults(): void
    {
        Date::use(CarbonImmutable::class);

        DB::prohibitDestructiveCommands(
            app()->isProduction(),
        );

        Password::defaults(fn (): ?Password => app()->isProduction()
            ? Password::min(12)
                ->mixedCase()
                ->letters()
                ->numbers()
                ->symbols()
                ->uncompromised()
            : null,
        );
    }
}