<?php

namespace App\Jobs;

use App\Models\Order;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue; //telling Laravel: "don't run this class directly — put it on the jobs table and let a background worker run it when free
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class SendOrderConfirmationEmail implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;
    //the Job receives the order it's sending an email about when it's created
    public function __construct(public Order $order)
    {
    }

    public function handle(): void
    {
        // Simulated delay so the queue effect is visible while learning —
        // remove this once you're satisfied it's actually queuing.
        sleep(3);
        //this is temporary, just for learning — we add a fake 3-second delay so you can visibly see the difference between "the response arrives instantly" and "the Job runs a moment later.
        // " In a real project, remove it — the natural delay (actually sending the email) is enough


        //ends a simple plain-text email. Since your .env has MAIL_MAILER=log,
        //  the email is never actually delivered to anyone — it just gets written in full to storage/logs/laravel.log, completely safe for local testing
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