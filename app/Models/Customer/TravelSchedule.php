<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class TravelSchedule extends Model
{
    protected $filable = [
        'kota_from_id',
        'kota_to_id',
        'departure_start',
        'departure_finish',
        'quota',
        'ticket_price'
    ];
}
