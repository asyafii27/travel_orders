<?php

namespace App\Http\Controllers\Api\Master;

use Illuminate\Http\Request;
use App\Models\Master\Provinsi;
use App\Http\Controllers\Api\System\BaseController;

class ProvinsiController extends BaseController
{
    /**
     * module has permission
     *
     * @var string
     */
    protected $permission = "Master.Privinsi.";

     /**
     * Module eloquent model.
     *
     * @var string
     **/
    protected $model = Provinsi::class;

    
    /**
     * Module index relation rule
     *
     * @var string
     **/
    protected $relationRule  = [];

     /**
     * Module index create rule
     *
     * @var string
     **/
    protected $createRule = [
        'name'
    ];

    /**
     * Module index create rule
     *
     * @var string
     **/
    protected $updateRule = [
        'id' => 'required|numeric',
        'name' => 'required|max:20'
    ];
    
    /**
     * Module index order rule
     *
     * @var string
     **/
    protected $orderRule = [
        'name' => 'created_at',
        'operator' => 'desc'
    ];

}
