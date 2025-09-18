<?php

namespace Modules\EBilling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\EBilling\Enums\TicketPriority;
use Modules\EBilling\Enums\TicketStatus;
use Modules\EBilling\Models\Customer;
use Modules\EBilling\Models\Ticket;

class TicketFactory extends Factory
{
    protected $model = Ticket::class;

    public function definition(): array
    {
        return [
            'code' => Ticket::generateCode(),
            'customer_id' => Customer::factory(),
            'subject' => $this->faker->sentence(6),
            'status' => $this->faker->randomElement(array_map(fn ($c) => $c->value, TicketStatus::cases())),
            'priority' => $this->faker->randomElement(array_map(fn ($c) => $c->value, TicketPriority::cases())),
            'last_activity_at' => now(),
        ];
    }
}
