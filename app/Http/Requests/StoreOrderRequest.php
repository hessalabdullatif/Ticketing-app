<?php

namespace App\Http\Requests;

use App\Models\Ticket;
use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Contracts\Validation\Validator;

class StoreOrderRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'event_id' => ['required', 'integer', 'exists:events,id'],
            'ticket_id' => ['required', 'integer', 'exists:tickets,id'],
            'quantity' => ['required', 'integer', 'min:1'],
        ];
    }
//an extra check that runs after all the basic rules() pass. We fetch the ticket from the database and confirm the event_id sent actually matches that ticket's real event.
//This prevents a weird case: someone sending a ticket_id that belongs to "Event A" but pairing it with event_id for "Event B"
    public function withValidator(Validator $validator): void
    {
        $validator->after(function ($validator) {
            $ticket = Ticket::find($this->ticket_id);

            if ($ticket && (int) $ticket->event_id !== (int) $this->event_id) {
                $validator->errors()->add('ticket_id', 'This ticket does not belong to the selected event.');
            }
        });
    }
}
