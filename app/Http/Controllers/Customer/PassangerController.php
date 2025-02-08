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

use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Customer\Passanger;
use App\Http\Controllers\Api\System\BaseController;

class PassangerController extends BaseController
{
    /**
     * Module base permission.
     *
     * @var string
     **/
    protected $permission = "Customer.Passanger.";

    /**
     * Module eloquent model.
     *
     * @var string
     **/
    protected $model = Passanger::class;

    /**
     * Module index order rule
     *
     * @var string
     **/
    protected $orderRule = [
        'name' => 'created_at',
        'operator' => 'desc'
    ];

     /**
     * Module index create rule
     *
     * @var string
     **/
    protected $createRule = [
        'name'  => 'required|max:200',
        'email' => 'max:200|email',
        'address' => 'required|min:8|max:200',
        'password' => 'required'
    ];

    /**
    * Module index create rule
    *
    * @var string
    **/
    protected $updateRule = [
        'id' => 'required|numeric',
        'name'  => 'required|max:200',
        'email' => 'max:200|email',
        'address' => 'required|min:8|max:200',
        'password' => 'required'
    ];

     /**
     * Undocumented function
     *
     * @param Object $request
     * @return void
     */
    public function beforeCreateValidation($request)
    {
        $input = $request->all();

        $passanger = Passanger::find($input['id']);
        $samePassangerEmail = Passanger::where('email', $input['email'])->where('id', '!=', $passanger->id)->first();
        if (!empty($samePassangerEmail)) return "Email {$input['email']} telah digunakan oleh penumpang lain. Silakan gunakan Email yang lain!";

        return null;
    }


    /**
     * Undocumented function
     *
     * @param Array $request
     * @param Object $input
     * @return void
     */
    public function afterCreate($request, $input)
    {
        User::create([
            'reff_id' => $input->id,
            'role_id' => 2,
            'name' => $input->name,
            'email' => $input->email,
            'password' => $request['password']
        ]);

    }

    /**
     * Undocumented function
     *
     * @param Object $request
     * @return void
     */
    public function beforeUpdateValidation($request)
    {
        $input = $request->all();

        $passanger = Passanger::find($input['id']);
        $samePassangerEmail = Passanger::where('email', $input['email'])->where('id', '!=', $passanger->id)->first();
        if (!empty($samePassangerEmail)) return "Email {$input['email']} telah digunakan oleh penumpang lain. Silakan gunakan Email yang lain!";

        return null;
    }

     /**
     * Undocumented function
     *
     * @param Array $request
     * @param Object $input
     * @return void
     */
    public function afterUpdate($request, $input)
    {
        $user = User::where('role_id', 2)->where('reff_id', $input->id)->first();
        if (empty($user)) return $this->errorResponse(null, 422, 'User tidak ditemukan');

        $userSameEmail = user::where('email', $input->email)->where('id', '!=', $user->id)->first();
        if (!empty($userSameEmail)) return $this->errorResponse(null, 422, 'Email ' . $input->email . ' telah digunakan oleh user lain. Silakan gunakan email yang lain!');

        $user->update([
            'reff_id' => $input->id,
            'role_id' => 2,
            'name' => $input->name,
            'email' => $input->email,
            'password' => $request['password']
        ]);

    }

}
