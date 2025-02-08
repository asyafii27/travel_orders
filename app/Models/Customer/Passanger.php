<?php

namespace App\Models\Customer;

use Illuminate\Database\Eloquent\Model;

class Passanger extends Model
{

    /**
    * The attributes that are mass assignable.
    *
    * @var array
    */
    protected $fillable = [
      'name',
      'email',
      'phone',
      'address',
      'created_by',
      'updated_by'
    ];

     /** 
    * Relasi dengan tabel ticket_orders
    * 
    * @return \Illuminate\Database\Eloquent\Relations\hasMany
    */
    public function ticketOrder()
    {
       $this->hasMany(TicketOrder::class, 'ticket_order_id', 'id');
    }
}
