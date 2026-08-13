<?php

namespace App\Services\PaymentGateways;

use Illuminate\Support\Facades\Log;

class FakeVisa implements PaymentGateway
{
    public function charge(float $amount): bool
    {
        Log::info("FakeVisa gateway charged {$amount} SAR");

        return true;
    }
}