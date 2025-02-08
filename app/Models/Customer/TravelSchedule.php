<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class TravelSchedule extends Model
{
    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
        'regency_from_id',
        'regency_to_id',
        'departure_start',
        'departure_finish',
        'quota',
        'ticket_price',
        'created_by',
        'updated_by'
    ];
}
