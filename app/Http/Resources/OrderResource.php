<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class OrderResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {

    return [
        'id' => $this->id,
        'quantity' => $this->quantity,
        'total_price' => $this->total_price,
        'status' => $this->status,
        'ticket' => [
            'id' => $this->ticket->id,
            'type' => $this->ticket->type,
            'event' => $this->ticket->event->name,
        ],
        'created_at' => $this->created_at,
    ];
}    }

