<?php

namespace App\Services\PaymentGateways;

use Illuminate\Support\Facades\Log;

class FakeMada implements PaymentGateway
{
    public function charge(float $amount): bool
    {
        Log::info("FakeMada gateway charged {$amount} SAR");

        return true;
    }
}