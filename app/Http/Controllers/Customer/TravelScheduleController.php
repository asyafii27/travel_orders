<?php

/**
 * Console API
 *
 * PHP version 8.3
 *
 * @category Modules
 * @package  App
 * @author   asyafii27
 * @license  https://opensource.org/licenses/MIT MIT
 * */

namespace App\Http\Controllers\Customer;

use App\Http\Controllers\Api\System\BaseController;
use App\Models\Customer\TravelSchedule;
use Illuminate\Http\Request;

class TravelScheduleController extends BaseController
{
    /**
     * module base permission
     *
     * @var string
     */
    protected $permission = 'Customer.TravelSchedule.';

    /**
     * module eloquent model;
     *
     * @var Model
     */
    protected $model = TravelSchedule::class;

    /**
     * module index order rule
     *
     * @var array
     */
    protected $orderRule = [
        'name' => 'created_at',
        'operator' => 'desc'
    ];

    /**
     * module relation rule
     *
     * @var array
     */
    protected $relationRule = [
        
    ];

    /**
     * module index create Rule
     *
     * @var array
     */
    protected $createRule = [
        'regency_from_id' => 'required|numeric',
        'regency_to_id' => 'required|numeric',
        'departure_start' => 'required',
        'departure_finish' => 'required',
        'quota' => 'required',
        'ticket_price' => 'required'
    ];

    /**
     * module index update Rule
     *
     * @var array
     */
    protected $updateRule = [
        'id' => 'required|numeric',
        'regency_from_id' => 'required|numeric',
        'regency_to_id' => 'required|numeric',
        'departure_start' => 'required',
        'departure_finish' => 'required',
        'quota' => 'required',
        'ticket_price' => 'required'
    ];
}
