<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Ticket;
use App\Services\PaymentGateways\PaymentGateway;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;
use App\Jobs\SendOrderConfirmationEmail;
class OrderService
{
      public function __construct(private PaymentGateway $paymentGateway)
    {
    }
    //this is the single function the controller will call. It receives validated data
    public function createOrder(array $data): Order
    {
        //wraps all the following steps (check, deduct, save) in "one transaction." 
        return DB::transaction(function () use ($data) {
            $ticket = Ticket::lockForUpdate()->findOrFail($data['ticket_id']);
        //this is exactly the "over-ordering" check the Gate asks for
            if ($ticket->quantity_available < $data['quantity']) {
                throw ValidationException::withMessages([
                    'quantity' => "Only {$ticket->quantity_available} ticket(s) left for this type.",
                ]);
            }
             //this is the only place in the entire project where the total gets calculated
            $total = $this->calculateTotal($ticket, $data['quantity']);

           $this->paymentGateway->charge($total);

             //reduces the available quantity by the requested amount directly in the database
            $ticket->decrement('quantity_available', $data['quantity']);

            $order = Order::create([
                'ticket_id' => $ticket->id,
                //
                'user_id' => auth()->id(),
                'quantity' => $data['quantity'],
                'total_price' => $total,
                'status' => Order::STATUS_PAID,
            ]);
            //step 4
            SendOrderConfirmationEmail::dispatch($order)->afterCommit();

              
            Log::info("Order #{$order->id} created for ticket #{$ticket->id}, total: {$total}");

            return $order;
        });
    }
    public function calculateTotal(Ticket $ticket, int $quantity): float
{
    return (float) $ticket->price * $quantity;
}

}