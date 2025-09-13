<?php

namespace Modules\EBilling\Database\Factories;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\EBilling\Models\Ticket;
use Modules\EBilling\Models\TicketMessage;

class TicketMessageFactory extends Factory
{
    protected $model = TicketMessage::class;

    public function definition(): array
    {
        return [
            'ticket_id' => Ticket::factory(),
            'author_name' => $this->faker->name(),
            'message' => $this->faker->paragraph(),
        ];
    }
}
