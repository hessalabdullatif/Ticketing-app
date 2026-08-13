<?php

namespace App\Services\PaymentGateways;

interface PaymentGateway
{
    public function charge(float $amount): bool;
}