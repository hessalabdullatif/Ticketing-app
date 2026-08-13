<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    public function __construct(public Order $order)
    {
    }

    public function handle(): void
    {
        // Simulated delay so the queue effect is visible while learning —
        // remove this once you're satisfied it's actually queuing.
        sleep(3);

        Mail::raw(
            "Thanks for your order #{$this->order->id}! Total: \${$this->order->total_price}.",
            function ($message) {
                $message->to('guest@example.com')
                    ->subject("Order #{$this->order->id} Confirmation");
            }
        );

        Log::info("Confirmation email sent for order #{$this->order->id}");
    }
}