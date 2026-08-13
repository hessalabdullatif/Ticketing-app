<?php

use App\Models\Event;
use App\Models\Ticket;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;

uses(RefreshDatabase::class);

it('creates an order and returns 201 with computed total', function () {
    $user = User::factory()->create();
    $event = Event::factory()->has(Ticket::factory()->state(['price' => 100, 'quantity_available' => 10]))->create();
    $ticket = $event->tickets->first();

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/orders', [
        'event_id' => $event->id,
        'ticket_id' => $ticket->id,
        'quantity' => 2,
    ]);

    $response->assertStatus(201)
        ->assertJsonPath('data.total_price', '200.00');
});

it('rejects invalid order data with 422', function () {
    $user = User::factory()->create();

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/orders', [
        'event_id' => null,
        'ticket_id' => null,
        'quantity' => 0,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['event_id', 'ticket_id', 'quantity']);
});

it('rejects over-ordering with 422', function () {
    $user = User::factory()->create();
    $event = Event::factory()->has(Ticket::factory()->state(['quantity_available' => 5]))->create();
    $ticket = $event->tickets->first();

    $response = $this->actingAs($user, 'sanctum')->postJson('/api/orders', [
        'event_id' => $event->id,
        'ticket_id' => $ticket->id,
        'quantity' => 999,
    ]);

    $response->assertStatus(422)
        ->assertJsonValidationErrors(['quantity']);
});

it('blocks unauthenticated users with 401', function () {
    $response = $this->postJson('/api/orders', [
        'event_id' => 1,
        'ticket_id' => 1,
        'quantity' => 1,
    ]);

    $response->assertStatus(401);
});

it('only returns the authenticated user\'s own orders', function () {
    $userA = User::factory()->create();
    $userB = User::factory()->create();

    $event = Event::factory()->has(Ticket::factory()->state(['quantity_available' => 10]))->create();
    $ticket = $event->tickets->first();

    // userA places an order
    $this->actingAs($userA, 'sanctum')->postJson('/api/orders', [
        'event_id' => $event->id,
        'ticket_id' => $ticket->id,
        'quantity' => 1,
    ]);

    // userB checks their own orders — should be empty
    $response = $this->actingAs($userB, 'sanctum')->getJson('/api/orders');

    $response->assertStatus(200)
        ->assertJsonCount(0, 'data');
});