<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EventResource extends JsonResource
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
        'name' => $this->name,
        'description' => $this->description,
        'venue' => $this->venue,
        'city' => $this->city,
        'starts_at' => $this->starts_at,
        'tickets' => TicketResource::collection($this->whenLoaded('tickets')),
    ];
    }
}
