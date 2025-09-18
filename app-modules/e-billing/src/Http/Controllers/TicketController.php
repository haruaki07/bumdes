<?php

namespace Modules\EBilling\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Validation\Rule;
use Modules\EBilling\Enums\TicketPriority;
use Modules\EBilling\Enums\TicketStatus;
use Modules\EBilling\Models\Customer;
use Modules\EBilling\Models\Ticket;
use Modules\EBilling\Models\TicketMessage;
use Modules\EBilling\Models\User;

class TicketController
{
    public function index()
    {
        $tickets = Ticket::with(['customer', 'assignee'])->orderByDesc('created_at')->paginate(15);

        return view('e-billing::tickets.index', compact('tickets'));
    }

    public function create()
    {
        $customers = Customer::orderBy('name')->get(['id', 'name', 'customer_id']);
        $users = User::orderBy('name')->get(['id', 'name']);
        $priorities = TicketPriority::cases();
        $selectedCustomerId = request('customer_id');

        return view('e-billing::tickets.create', compact('customers', 'users', 'priorities', 'selectedCustomerId'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'customer_id' => ['required', Rule::exists('ebil_customers', 'id')],
            'subject' => ['required', 'string', 'max:255'],
            'priority' => ['required', Rule::in(array_map(fn ($c) => $c->value, TicketPriority::cases()))],
            'assigned_to' => ['nullable', Rule::exists('ebil_users', 'id')],
            'message' => ['nullable', 'string'],
        ]);

        $ticket = Ticket::create([
            ...$data,
            'status' => TicketStatus::OPEN,
        ]);

        if (! empty($data['message'])) {
            TicketMessage::create([
                'ticket_id' => $ticket->id,
                'user_id' => auth('ebil')->id() ?? null,
                'message' => $data['message'],
            ]);
        }

        return redirect()->route('e-billing.tickets.show', $ticket)->with('success', 'Tiket berhasil dibuat.');
    }

    public function show(Ticket $ticket)
    {
        $ticket->load(['customer', 'assignee', 'messages.user']);
        $users = User::orderBy('name')->get(['id', 'name']);

        return view('e-billing::tickets.show', compact('ticket', 'users'));
    }

    public function update(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'subject' => ['required', 'string', 'max:255'],
            'priority' => ['required', Rule::in(array_map(fn ($c) => $c->value, TicketPriority::cases()))],
            'status' => ['required', Rule::in(array_map(fn ($c) => $c->value, TicketStatus::cases()))],
            'assigned_to' => ['nullable', Rule::exists('ebil_users', 'id')],
        ]);

        if (($data['status'] ?? null) === TicketStatus::RESOLVED->value && $ticket->resolved_at === null) {
            $ticket->resolved_at = now();
        }
        if (($data['status'] ?? null) === TicketStatus::CLOSED->value && $ticket->closed_at === null) {
            $ticket->closed_at = now();
        }

        $ticket->fill($data);
        $ticket->save();

        return back()->with('success', 'Tiket diperbarui.');
    }

    public function destroy(Ticket $ticket)
    {
        $ticket->delete();

        return redirect()->route('e-billing.tickets.index')->with('success', 'Tiket dihapus.');
    }

    public function addMessage(Request $request, Ticket $ticket)
    {
        $data = $request->validate([
            'message' => ['required', 'string'],
        ]);

        TicketMessage::create([
            'ticket_id' => $ticket->id,
            'user_id' => auth('ebil')->id() ?? null,
            'message' => $data['message'],
        ]);

        $ticket->last_activity_at = now();
        $ticket->save();

        return back()->with('success', 'Pesan ditambahkan.');
    }
}
