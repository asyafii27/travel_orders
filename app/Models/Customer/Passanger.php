<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class Passanger extends Model
{
    use model;

    protected $fillable = [
        'name',
        'email',
        'address'
    ];

    public function ticketOrder()
    {
        $this->hasMany(TicketOrder::class, 'ticket_order_id', 'id');
    }
}
