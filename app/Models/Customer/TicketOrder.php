<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class TicketOrder extends Model
{
    protected $fillable = [
        'passanger_id',
        'travel_schedule_id',
        'status'
    ];
}
