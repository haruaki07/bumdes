<?php

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\EBilling\Database\Factories\CustomerFactory;
use Modules\EBilling\Database\Factories\TicketFactory;
use Modules\EBilling\Database\Factories\UserFactory;
use Modules\EBilling\Models\Ticket;

uses(RefreshDatabase::class);

it('can create a ticket', function () {
    $user = UserFactory::new()->create();
    $this->actingAs($user, 'ebil');
    $customer = CustomerFactory::new()->create();

    $response = $this->post(route('e-billing.tickets.store'), [
        'customer_id' => $customer->id,
        'subject' => 'Internet lambat',
        'priority' => 'high',
    ]);

    $response->assertRedirect();
    expect(Ticket::count())->toBe(1);
});

it('can add a message to ticket', function () {
    $user = UserFactory::new()->create();
    $this->actingAs($user, 'ebil');
    $ticket = TicketFactory::new()->create();

    $response = $this->post(route('e-billing.tickets.messages.store', $ticket), [
        'message' => 'Kami sedang mengecek jaringan.',
    ]);

    $response->assertRedirect();
    $ticket->refresh();
    expect($ticket->messages()->count())->toBe(1);
});
