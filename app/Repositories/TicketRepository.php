<?php

namespace App\Repositories;

use App\Models\Ticket;
use App\Repositories\Interfaces\TicketRepositoryInterface;

class TicketRepository implements TicketRepositoryInterface
{
    public function all()
    {
        return Ticket::latest()->get();
    }

    public function create(array $data)
    {
        return Ticket::create($data);
    }

    public function find(int $id)
    {
        return Ticket::findOrFail($id);
    }

    public function update(int $id, array $data)
    {
        $ticket = $this->find($id);

        $ticket->update($data);

        return $ticket;
    }

    public function delete(int $id)
    {
        return Ticket::destroy($id);
    }
}