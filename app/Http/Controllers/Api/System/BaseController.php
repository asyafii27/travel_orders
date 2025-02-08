<?php

/**
 * Console API
 *
 * PHP version 7.4
 *
 * @category Modules
 * @package  App
 * @author   E-Solution <info@elgibor-solution.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://elgibor-solution.com
 */

namespace App\Http\Controllers\Api\System;

use App\Tenant;
use App\Traits\ApiResponse;
use Illuminate\Support\Str;

use Illuminate\Http\Request;

use App\Events\DataUpdateEvent;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;

/**
 * Base Module
 *
 * Base Module to manage CRUD
 * version 1.2
 *
 * @category Controller
 * @package  App\Http\Controllers\Api\Console
 * @author   E-Solution <info@elgibor-solution.com>
 * @license  https://opensource.org/licenses/MIT MIT
 * @link     https://elgibor-solution.com
 */

abstract class BaseController extends Controller
{
    use ApiResponse;

    /**
     * Module base permission.
     *
     * @var string
     */
    protected $permission = "";

    /**
     * Module eloquent model
     *
     * @var object
     */
    protected $model = "";

    // public $journal;

    /**
     * Module eloquent relation function
     *
     * @var string
     */
    protected $detailRelation = "";
    protected $bonusRelation = "";
    protected $syaratRelation = "";

    /**
     * Rows per page
     *
     * @var int
     */
    protected $paginationRows = 10;

    /**
     * Module create validation rule
     *
     * @var array
     */
    protected $createRule = [];

    /**
     * Module update validation rule
     *
     * @var array
     */
    protected $updateRule = [];

    /**
     * Module index relation rule
     *
     * @var array
     */
    protected $relationRule = [];

    /**
     * Module filter index relation rule
     *
     * @var array
     */
    protected $filterRelationRule = [];

    /**
     * Module detail relation rule
     *
     * @var array
     */
    protected $relationDetailRule = [];

    /**
     * Module index validation rule
     *
     * @var array
     */
    protected $filterRule = [];

    /**
     * Module index order rule
     *
     * @var array
     */
    protected $orderRule = [];

    /**
     * Upload Folder under ./storage/app
     *
     * @var string
     */
    protected $uploadDir = "";

    /**
     * Data di akses Per perusahaan
     *
     * @var boolean
     */
    protected $filterByBranchId = false;

    /**
     * Data di akses Per cabang
     *
     * @var boolean
     */
    protected $filterByCorpId = false;

    /**
     * Data di akses Per cabang
     *
     * @var boolean
     */
    protected $filterByBranch = false;

    /**
     *
     * Data di akses Per Customer
     *
     * @var boolean
     */
    protected $filterByCustomer = false;

    /**
     * declare variable for Data Utama / Master
     * @array
     */
    protected $master = [];

    /**
     *
     * Data di akses Per Barang FI
     *
     * @var boolean
     */
    protected $filterKategoryBarang = false;

    /**
     *
     * Data di akses Per Divisi FI
     *
     * @var boolean
     **/
    protected $filterByDivisi  = false;

    /**
     *
     * Data di akses Per Supir
     *
     * @var boolean
     */
    protected $filterBySupir = false;

    /**
     *
     * Broadcast
     *
     * @var boolean
     */
    protected $broadcast = false;

    /**
     *
     * Angular Cache Name
     *
     * @var string
     */
    protected $cacheName = "";

    /**
     *
     * Locked Module
     *
     * @var string
     */
    protected $lockedModule = [];

    /**
     *
     * 
     * Move File
     * @var array
     */
    protected $moveFileDatas = [];

    /**
     *
     * APP MODE
     *
     * @var string
     */
    protected $debug = false;

    /**
     * Return module data
     *
     * @param Request $request Request Object
     * @param Array $query   Eloquent array
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($this->debug) {
            Log::debug("---------------------------------------");
            Log::debug("Index => Check permission");
        }

        // $request->user()->hasPermission($this->permission . 'View');

        if ($this->debug) Log::debug("Index => Permission [OK]");

        $inputs = $request->all();

        $inputs = $this->beforeSearch($inputs);

        if ($this->debug) Log::debug("Index => Before Search [OK]");

        $model = new $this->model;

        $query = $model::query();

        // Relation Query builder
        if (count($this->relationRule) > 0) {

            if ($this->debug) Log::debug("Index => check relationRule [FOUND]");

            if (count($this->filterRelationRule) > 0) {

                if ($this->debug) Log::debug("Index => check filterRelationRule [FOUND]");

                $newFil = [];

                foreach ($this->relationRule as $rv) {

                    $relArr = explode(".", $rv);
                    $frn = $relArr[0];

                    if (isset($this->filterRelationRule[$frn])) {

                        $fil = $this->filterRelationRule[$frn];
                        $newFil[] = $fil;

                        $query->with([$frn => function ($q) use ($fil, $inputs, $relArr) {

                            foreach ($fil as $fk => $fv) {
                                $inp = isset($inputs[$fv['name']]) ? $inputs[$fv['name']] : false;

                                $inp = urldecode($inp);

                                if ($fv['operator'] == 'like') {
                                    $inp = "%$inp%";
                                }

                                if ($fv['operator'] == "in") {
                                    $q->whereIn($fv['name'], explode(",", $inp));
                                } else {
                                    $q->where($fv['name'], $fv['operator'], $inp);
                                }

                                if (count($relArr) > 1) {
                                    unset($relArr[0]);
                                    $q->with($relArr);
                                }
                            }
                        }]);
                    } else {
                        $query->with($rv);
                    }
                }
                // return response()->json(["dd" =>$newFil]);
            } else {
                $query->with($this->relationRule);
            }
        }

        // Where / Like Query builder
        // if (isset($input['filters'])) {
        //     $filters = $inputs['filters'];

        //     foreach ($filters as $filter) {

        //         if (in_array($filter[0], $this->filterRule)) {

        //             if ($filter[2] !== "" && $filter[2] !== "undefined") {

        //                 if ($filter[1] === "in") {
        //                     $query->whereIn($filter[0], explode(",", $filter[2]));
        //                 } else {
        //                     $query->where($filter[0], $filter[1], $filter[2]);
        //                 }

        //             }
        //         }
        //     }
        // }

        foreach ($this->filterRule as $rule) {

            $input = isset($inputs[$rule['name']]) ? $inputs[$rule['name']] : false;
            $input = urldecode($input);

            if ($input != '' && $input != 'null' && $input != 'undefined') {
                if ($rule['operator'] == 'like') {
                    $input = "%$input%";
                }

                if ($rule['operator'] == "in") {
                    $query->whereIn($rule['name'], explode(",", $input));
                } else {
                    $query->where($rule['name'], $rule['operator'], $input);
                }
            }
        }

        if ($this->debug) Log::debug("Index => check filterRule [OK]");

        if ($this->filterByCorpId) {
            if (!$request->user()->checkPermission('Permissions.Access.Full')) {
                $query->where('perusahaan_id', '=', $request->user()->perusahaan_id);
            } elseif (isset($request->perusahaan_id) && !empty($request->perusahaan_id) && $request->perusahaan_id != "null") {
                $perusahaan_id = urldecode($request->perusahaan_id);
                $query->where('perusahaan_id', '=', $perusahaan_id);
            }

            if ($this->debug) Log::debug("Index => check filterByCorpId [OK]");
        }

        if ($this->filterByBranchId) {
            if (!$request->user()->checkPermission('Permissions.Access.Full')) {
                $query->where('cabang_id', '=', $request->user()->cabang_id);
            } elseif (isset($request->cabang_id) && !empty($request->cabang_id) && $request->cabang_id != "null") {
                $cabang_id = urldecode($request->cabang_id);
                $query->where('cabang_id', '=', $cabang_id);
            }

            if ($this->debug) Log::debug("Index => check filterByBranchId [OK]");
        }

        if ($this->filterByBranch) {
            if (!$request->user()->checkPermission('Permissions.Access.Full')) {
                $query->where('cabang_txt', '=', $request->user()->cabang_txt);
            } elseif (!empty($cabang) && $cabang != "null") {
                $cabang = urldecode($cabang);
                $query->where('cabang_txt', '=', $cabang);
            }

            if ($this->debug) Log::debug("Index => check filterByBranch [OK]");
        }

        if ($this->filterByCustomer) {
            if (!$request->user()->checkPermission('Permissions.Access.Full') && $request->user()->roles_id == "18") {
                $query->where('customer_id', '=', $request->user()->reff_id);
            }

            if ($this->debug) Log::debug("Index => check filterByCustomer [OK]");
        }

        if ($this->filterKategoryBarang) {
            if (!empty($inputs)) {
                if ($inputs['type'] != '7') {
                    if (!$request->user()->checkPermission('Permissions.Access.Full')) {
                        $userDivisi = $request->user()->userDivisi;
                        $productGroup = [];
                        foreach ($userDivisi as $key => $divisiValue) {
                            foreach ($divisiValue->divisiPenjualan->divisiPenjualanDetail as $keys => $values) {
                                $productGroup[] = $values->product_group_id;
                            }
                        }
                        if ($this->model == "App\Models\Inventory\ProductGroups") {
                            $query->whereIn('id', $productGroup);
                        } else {
                            $query->whereIn('product_group_id', $productGroup);
                        }

                        if ($this->debug) Log::debug("Index => check filterKategoryBarang [OK]");
                    }
                }
            }
        }

        if ($this->filterByDivisi) {
            if (!$request->user()->checkPermission('Permissions.Access.Full')) {
                $user = $request->user();
                // $userDivisi = $request->user()->userDivisi;
                $userDivisi = $request->user()->userDivisi()->where('cabang_id', $user->cabang_id)->get();
                $userDivisis = [];
                foreach ($userDivisi as $key => $divisiValue) {
                    $userDivisis[] = $divisiValue->divisi_penjualan_id;
                }
                // print_r($userDivisis);die;

                $query->whereIn('divisi_penjualan_id', $userDivisis);

                if ($this->debug) Log::debug("Index => check filterByDivisi [OK]");
            }
        }

        if ($this->filterBySupir) {
            if (!$request->user()->checkPermission('Permissions.Access.Full') && $request->user()->roles_id == "13") {
                $query->where('id', '=', $request->user()->reff_id);

                if ($this->debug) Log::debug("Index => check filterBySupir [OK]");
            }
        }

        $query = $this->customSearch($request, $query);

        if ($this->debug) Log::debug("Index => check customSearch [OK]");

        // Sorting Query builder
        if (isset($this->orderRule[0]['name'])) {
            foreach ($this->orderRule as $row) {
                if (isset($row['tipe']) && $row['tipe'] == 'raw') {
                    $query->orderByRaw($row['order']);
                } else {
                    $query->orderBy($row['name'], $row['operator']);
                }
            }
        } else {
            $query->orderBy($this->orderRule['name'], $this->orderRule['operator']);
        }

        if ($this->debug) Log::debug("Index => check orderRule [OK]");

        $sql = Str::replaceArray('?', $query->getBindings(), $query->toSql());

        if ($this->debug) Log::debug("Query => {$sql}");

        if (isset($inputs['page']) && !empty($inputs['page'])) {
            $list = $query->paginate($this->paginationRows);

            if ($this->debug) Log::debug("Index => do pagination [OK]");

            $list = $this->afterSearch($request, $list);

            if ($this->debug) {
                Log::debug("Index => check afterSearch [OK]");
                Log::debug("---------------------------------------");
            }

            return $list;
        }

        $list = $query->get();

        if ($this->debug) Log::debug("Index => do Query [OK]");

        $list = $this->afterSearch($request, $list);

        if ($this->debug) {
            Log::debug("Index => do afterSearch [OK]");
            Log::debug("---------------------------------------");
        }

        return $this->successResponse("OK", $list);
    }

    /**
     * Show single data
     *
     * @param Request $request Request Object
     *
     * @return \Illuminate\Http\Response
     */
    public function show(Request $request, $primaryId)
    {
    }

    /**
     * Create a new data
     *
     * @param Request $request Request Object
     *
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        if ($this->debug) {
            Log::debug("---------------------------------------");
            Log::debug("store => check hasPermission");
        }

        // $request->user()->hasPermission($this->permission . 'Create');

        if ($this->debug) Log::debug("store => check hasPermission [OK]");

        // Log::info($request->all());die;


        $input = $request->validate($this->createRule);

        $invalid = $this->beforeCreateValidation($request);

        if ($this->debug) Log::debug("store => check beforeCreateValidation [OK]");

        if (!empty($invalid)) {
            if (is_array($invalid)) {
                return $this->errorResponse($invalid);
            } else {
                return $this->errorResponse([], 422, $invalid);
            }
        }

        if ($this->debug) Log::debug("store => check validate [OK]");

        try {
            if (!empty(tenant('id'))) {
                \DB::beginTransaction();
                \DB::connection('mysql')->beginTransaction();
            } else {
                \DB::beginTransaction();
            }

            $input = $this->beforeCreate($input);

            if ($this->debug) Log::debug("store => do beforeCreate [OK]");
            if ($this->debug) Log::debug("input =>" . print_r($input, true));

            $model = new $this->model;
            $input['created_by'] = $request->user()->name;

            $result = $model::create($input);

            if ($this->debug) Log::debug("store => do create [OK]");

            $this->afterCreate($request, $result);

            if ($this->debug) Log::debug("store => do afterCreate [OK]");

            if (!empty($request->get('dataDetail'))) {

                if ($this->debug) Log::debug("store => dataDetail [Found]");

                if (!empty($this->detailRelation)) {

                    if ($this->debug)  Log::debug("store => detailRelation [Found]");

                    $dataDetail = $request->get('dataDetail');

                    if (count($dataDetail) > 0) {

                        $dataDetail = $this->beforeCreateDetail($dataDetail);

                        if ($this->debug)  Log::debug("store => do beforeCreateDetail [OK]");

                        $dataDetail = $result->{$this->detailRelation}()->createMany($dataDetail);

                        if ($this->debug)  Log::debug("store => do createMany Detail [OK]");

                        $this->afterCreateDetail($dataDetail);

                        if ($this->debug)  Log::debug("store => do afterCreateDetail [OK]");
                    }
                } elseif ($this->debug) {
                    Log::debug("store => detailRelation [Empty]");
                }
            } elseif ($this->debug) {
                Log::debug("store => dataDetail [Empty]");
            }

            $this->triggerCreateEvent($result);

            if ($this->debug)  Log::debug("store => do triggerCreateEvent [OK]");

            if ($this->broadcast) {
                broadcast(new DataUpdateEvent([
                    'module' => $this->cacheName,
                    'action' => 'store'
                ]));

                if ($this->debug) {
                    Log::debug("store => do broadcast [OK]");
                }
            }

            if ($this->debug) Log::debug("---------------------------------------");

            if (!empty($this->moveFileDatas)) $this->moveUploadedFile();

            if (!empty(tenant('id'))) {
                \DB::commit();
                \DB::connection('mysql')->commit();
            } else {
                \DB::commit();
            }

            return $this->successResponse("OK", $result, 201);
        } catch (\Exception $e) {

            if (!empty(tenant('id'))) {
                \DB::rollback();
                \DB::connection('mysql')->rollback();
            } else {
                \DB::rollback();
            }

            Log::error("STORE => " . $e->getMessage());

            return $this->errorResponse([], 422, $e->getMessage());
        }
    }

    /**
     * Update Module Data
     *
     * @param Request $request    Request Object
     * @param Int     $primaryKey Primary ID
     *
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $primaryKey)
    {

        if ($this->debug) {
            Log::debug("---------------------------------------");
            Log::debug("update => check hasPermission");
        }

        // $request->user()->hasPermission($this->permission . 'Edit');

        if ($this->debug)  Log::debug("update => do hasPermission [OK]");


        $input = $request->validate($this->updateRule);

        if ($this->debug)  Log::debug("update => do validate [OK]");

        $invalid = $this->beforeUpdateValidation($request);

        if ($this->debug)  Log::debug("update => do beforeUpdateValidation [OK]");

        if (!empty($invalid)) {
            if (is_array($invalid)) {
                return $this->errorResponse($invalid);
            } else {
                return $this->errorResponse([], 422, $invalid);
            }
        }

        try {

            if (!empty(tenant('id'))) {
                \DB::beginTransaction();
                \DB::connection('mysql')->beginTransaction();
            } else {
                \DB::beginTransaction();
            }

            $input = $this->beforeUpdate($input);

            if ($this->debug)  Log::debug("update => do beforeUpdate [OK]");

            $model = new $this->model;
            $data  = $model->findOrFail($primaryKey);
            $input['updated_by'] = $request->user()->name;

            $data->update($input);

            if ($this->debug)  Log::debug("update => do update [OK]");

            $this->afterUpdate($request, $data);

            if ($this->debug)  Log::debug("update => do afterUpdate [OK]");

            if (!empty($request->get('dataDetail'))) {

                if ($this->debug)  Log::debug("update => dataDetail [FOUND]");

                if (!empty($this->detailRelation)) {

                    if ($this->debug)  Log::debug("update => detailRelation [FOUND]");

                    $dataDetail = $request->get('dataDetail');

                    if (count($dataDetail) > 0) {

                        $dataDetail = $this->beforeUpdateDetail($dataDetail);

                        if ($this->debug)  Log::debug("update => do beforeUpdateDetail [OK]");

                        $data->{$this->detailRelation}()->sync($dataDetail);
                        // $data->{$this->detailRelation}()->sync($dataDetail);

                        if ($this->debug)  Log::debug("update => do sync detail [OK]");

                        $this->afterUpdateDetail($dataDetail);

                        if ($this->debug)  Log::debug("update => do afterUpdateDetail [OK]");
                    }
                } elseif ($this->debug) {
                    Log::debug("update => detailRelation [EMPTY]");
                }
            } elseif ($this->debug) {
                Log::debug("update => dataDetail [EMPTY]");
            }

            $this->triggerUpdateEvent($data);

            if ($this->debug)  Log::debug("update => do triggerUpdateEvent [OK]");

            if ($this->broadcast) {
                broadcast(new DataUpdateEvent([
                    'module' => $this->cacheName,
                    'action' => 'update'
                ]));

                if ($this->debug) {
                    Log::debug("update => do broadcast [OK]");
                }
            }

            if ($this->debug) Log::debug("---------------------------------------");

            if (!empty($this->moveFileDatas)) $this->moveUploadedFile();

            if (!empty(tenant('id'))) {
                \DB::commit();
                \DB::connection('mysql')->commit();
            } else {
                \DB::commit();
            }

            return $this->successResponse("OK", $data);
        } catch (\Exception $e) {

            if (!empty(tenant('id'))) {
                \DB::rollback();
                \DB::connection('mysql')->rollback();
            } else {
                \DB::rollback();
            }

            Log::error("UPDATE => " . $e->getTraceAsString());
            Log::error("UPDATE => " . $e->getMessage());

            return $this->errorResponse([], 422, $e->getMessage());
        }
    }

    /**
     * Delete Module Data
     *
     * @param Request $request    Request Object
     * @param Int     $primaryKey Primary ID
     *
     * @return \Illuminate\Http\Response
     */
    public function destroy(Request $request, $primaryKey)
    {
        if ($this->debug) {
            Log::debug("---------------------------------------");
            Log::debug("destroy => Check Permission");
        }

        // $request->user()->hasPermission($this->permission . 'Delete');

        if ($this->debug)  Log::debug("destroy => do hasPermission [OK]");

        $invalid = $this->beforeDeleteValidation($request);

        if ($this->debug)  Log::debug("destroy => do beforeDeleteValidation [OK]");

        if (!empty($invalid)) return $invalid;

        try {

            if (!empty(tenant('id'))) {
                \DB::beginTransaction();
                \DB::connection('mysql')->beginTransaction();
            } else {
                \DB::beginTransaction();
            }

            $model = new $this->model;
            $data = $model->findOrFail($primaryKey);

            $this->beforeDelete($primaryKey);

            if ($this->debug)  Log::debug("destroy => do beforeDelete [OK]");

            if (!empty($request->get('dataDetail'))) {
                if (!empty($this->detailRelation)) {
                    $data->{$this->detailRelation}()->delete();
                }
            }

            $data->delete();

            if ($this->debug)  Log::debug("destroy => do delete [OK]");

            $this->afterDelete($primaryKey);

            if ($this->debug)  Log::debug("destroy => do afterDelete [OK]");

            if ($this->broadcast) {
                broadcast(new DataUpdateEvent([
                    'module' => $this->cacheName,
                    'action' => 'delete'
                ]));

                if ($this->debug)  Log::debug("destroy => do broadcast [OK]");
            }

            if ($this->debug) Log::debug("---------------------------------------");

            if (!empty(tenant('id'))) {
                \DB::commit();
                \DB::connection('mysql')->commit();
            } else {
                \DB::commit();
            }

            return $this->successResponse("OK", [], 204);
        } catch (\Exception $e) {

            if (!empty(tenant('id'))) {
                \DB::rollback();
                \DB::connection('mysql')->rollback();
            } else {
                \DB::rollback();
            }

            Log::error("DESTROY => " . $e->getMessage());

            return $this->errorResponse([], 422, $e->getMessage());
        }
    }

    /**
     * Return Data Detail
     *
     * @return json
     */
    public function detail(Request $request, $primaryKey)
    {
        $request->user()->hasPermission($this->permission . 'View');

        $model = new $this->model;
        $query = $model::query();

        // Relation Query builder
        if (count($this->relationDetailRule) > 0) {
            $query->with($this->relationDetailRule);
        }

        $query = $this->customDetail($request, $primaryKey, $query);

        $data = $query->findOrFail($primaryKey);

        $list = $data->{$this->detailRelation};

        Log::debug($data);

        return $this->successResponse("OK", $list, 200);
    }

    /**
     * Before Search Data Callback
     *
     * @param Array $input Params Array
     *
     * @return Array
     */
    public function beforeSearch($inputs)
    {
        return $inputs;
    }

    /**
     * After Search Data Callback
     *
     * @param Request $request Request Object
     * @param Array $input Params Array
     *
     * @return Array
     */
    public function afterSearch($request, $inputs)
    {
        return $inputs;
    }

    /**
     * Custom Search Data Callback
     *
     * @param Request $request Request Object
     * @param Array   $query   Eloquent Object
     *
     * @return Array
     */
    public function customSearch($request, $query)
    {
        return $query;
    }

    /**
     * Custom Detail Data Callback
     *
     * @param Request $request Request Object
     * @param Array   $query   Eloquent Object
     *
     * @return Array
     */
    public function customDetail($request, $primaryKey, $query)
    {
        return $query;
    }

    /**
     * Before Create Data Validation Callback
     *
     * @param Request $request    Request Object
     *
     * @return Array
     */
    public function beforeCreateValidation($request)
    {
        return null;
    }

    /**
     * Before Create Data Callback
     *
     * @param Array $input Params Array
     *
     * @return Array
     */
    public function beforeCreate($input)
    {
        return $input;
    }

    /**
     * After Create Data Callback
     *
     * @param Request $request    Request Object
     * @param Array $input Params Array
     *
     * @return Array
     */
    public function afterCreate($request, $input)
    {
    }

    /**
     * Event Trigger Data Callback
     *
     * @param Array $input Params Object from result
     *
     * @return Array
     */
    public function triggerCreateEvent($input)
    {
    }

    /**
     * Event Trigger Data Callback
     *
     * @param Array $input Params Object from result
     *
     * @return Array
     */
    public function triggerUpdateEvent($input)
    {
    }

    /**
     * Before Update Data Validation Callback
     *
     * @param Request $request    Request Object
     *
     * @return Array
     */
    public function beforeUpdateValidation($request)
    {
        return null;
    }

    /**
     * Before Update Data Callback
     *
     * @param Array $input Params Array
     *
     * @return Array
     */
    public function beforeUpdate($input)
    {
        return $input;
    }

    /**
     * After Update Data Callback
     *
     * @param Request $request    Request Object
     * @param Array $input Params Array
     *
     * @return Array
     */
    public function afterUpdate($request, $input)
    {
    }

    /**
     * Before Delete Data Validation Callback
     *
     * @param Request $request    Request Object
     *
     * @return Array
     */
    public function beforeDeleteValidation($request)
    {
        return null;
    }

    /**
     * before Delete Data Callback
     * @param Array $input Primary Key
     *
     * @return void
     */
    public function beforeDelete($input)
    {
    }

    /**
     * After Delete Data Callback
     *
     * @param String $input Params  Primary Key
     *
     * @return void
     */
    public function afterDelete($input)
    {
    }

    /**
     * Before Create Data Detail Callback
     *
     * @param Array $input Params Array
     *
     * @return Array
     */
    public function beforeCreateDetail($input)
    {
        return $input;
    }

    /**
     * After Create Data Detail Callback
     *
     * @param Array $input Params Array
     *
     * @return Array
     */
    public function afterCreateDetail($input)
    {
        return $input;
    }

    /**
     * Before Update Data Detail Callback
     *
     * @param Array $input Params Array
     *
     * @return Array
     */
    public function beforeUpdateDetail($input)
    {
        return $input;
    }

    /**
     * After Update Data Detail Callback
     *
     * @param Array $input Params Array
     *
     * @return Array
     */
    public function afterUpdateDetail($input)
    {
        return $input;
    }

    public function getModel()
    {
        return $this->model;
    }

    /**
     * Move uploaded file from GCS
     * 
     * @param  string  $filename
     * @param  string  $filepath
     * @return void
     */
    public function moveUploadedFile()
    {
        $disk = Storage::disk('gcs');

        foreach ($this->moveFileDatas as $filepath) {
            $filename = basename($filepath);
            if ($disk->exists('tmp/' . $filename)) {
                $disk->move('tmp/' . $filename, $filepath);
            }
        }
    }
}
