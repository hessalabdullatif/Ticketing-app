<?php

use App\Models\Ticket;
use App\Services\OrderService;
use App\Services\PaymentGateways\FakeMada;

it('calculates the correct total price for a given quantity', function () {
    $ticket = new Ticket(['price' => 150.50]);

    $service = new OrderService(new FakeMada());

    $total = $service->calculateTotal($ticket, 3);

    expect($total)->toBe(451.5);
});

it('returns zero when quantity is zero', function () {
    $ticket = new Ticket(['price' => 99.99]);

    $service = new OrderService(new FakeMada());

    $total = $service->calculateTotal($ticket, 0);

    expect($total)->toBe(0.0);
});