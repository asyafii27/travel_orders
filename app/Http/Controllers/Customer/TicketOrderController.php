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
use App\Models\Customer\TicketOrder;
use Illuminate\Http\Request;

class TicketOrderController extends BaseController
{
    /**
     * module base permission
     *
     * @var string
     */
    protected $permission = 'Customer.TicketOrder.';

    /**
     * module eloquent model
     *
     * @var [type]
     */
    protected $model = TicketOrder::class;

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
        'passanger_id' => 'required|numeric',
        'travel_schedule_id' => 'required|numeric',
        'status' => 'nullable'
    ];

    /**
     * module index update rule
     *
     * @var array
     */
    protected $updateRule = [
        'id' => 'required|numeric',
        'passanger_id' => 'required|numeric',
        'travel_schedule_id' => 'required|numeric',
        'status' => 'nullable'
    ];

    /**
     * Undocumented function
     *
     * @param [type] $request
     * @return void
     */
    public function beforeCreateValidation($request)
    {
        $input = $request->all();
        $ticketOrderCount = $this->model::where('travel_schedule_id', $input['travel_schedule_id'])->count();
        if ($ticketOrderCount >= 10) return 'Kuota pada jadwal ini telah penuh';

        return null;
    }

    /**
     * Undocumented function
     *
     * @param [type] $input
     * @return void
     */
    public function beforeCreate($input)
    {
        $input['status'] = 'pending';

        return $input;
    }

    /**
     * Undocumented function
     *
     * @param [type] $request
     * @return void
     */
    public function beforeUpdateValidation($request)
    {
        $input = $request->all();
        $ticketOrderCount = $this->model::where('travel_schedule_id', $input['id'])->count();
        if ($ticketOrderCount >= 0) return 'Kuota pada jadwal ini telah penuh';

        return null;

    }

    /**
     * Undocumented function
     *
     * @param [type] $input
     * @return void
     */
    public function beforeUpdate($input)
    {
        $input['status'] = 'pending';

        return $input;
    }


}
