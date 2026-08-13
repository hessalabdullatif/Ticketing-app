<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Resources\EventResource;
use App\Models\Event;
use Illuminate\Http\Request;

class EventController extends Controller
{
   public function index(Request $request)
{
    $events = Event::query()
        ->when($request->filled('city'), fn ($q) => $q->where('city', $request->string('city')))
        ->orderBy('starts_at')
        ->get();

    return EventResource::collection($events);

    }

    public function show(Event $event)
    {
        return new EventResource($event->load('tickets'));
    }
}
