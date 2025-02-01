<?php

namespace App\Http\Controllers\Api\User;

use App\Models\User;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;

class UserController extends Controller
{

    protected $permission = 'User.DataUser.';

    protected $paginate = 10;
    protected $model = User::class;


    public function index(Request $request)
    {
        $request->user()->hasPermission($this->permission . 'View');

        $input = $request->all();

        $query = $this->model::query();

        if (!empty($input['page'])) {
            $lists = $query->paginate($this->paginate);
        } else {

            $lists = $query->get();
        }


        return $lists;
    }
}
