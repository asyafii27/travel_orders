<?php

namespace App\Http\Controllers\Api;

use Exception;
use App\Models\User;
use Illuminate\Http\Request;
use App\Models\Company\Worker;
use App\Models\Company\Manager;
use App\Models\Company\Perusahaan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\Controller;
use App\Models\Customer\Passanger;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');
        if (!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['error' => 'Unauthenticate'], 401);
        }

        return response()->json([
            'token' => $token,
            'token_type' => 'jwt',
            'expires_in' => auth('api')->factory()->getTTL() * 60
        ]);
    }

    public function register(Request $request)
    {
        $input = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
            'phone' => 'required|string|max:15',
            'role_id' => 'required|numeric',
            'address' => 'required'
        ]);

        if ($input->fails()) {
            return response()->json(['errors' => $input->errors()], 422);
        }
        
        try {
            DB::beginTransaction();

            $input = $input->validated();
            $input['password'] = Hash::make($input['password']);
            $input['role_id'] = $input['role_id'];

            $user = User::create($input);
            
            if ($input['role_id'] == 2) {
                $inputPassanger = [
                    'name' => $input['name'],
                    'email' => $input['email'],
                    'phone' => $input['phone'],
                    'address' => $input['address']
                ];
                $passanger = Passanger::create($inputPassanger);

                $user->update(['reff_id' => $passanger->id]);
            }

            DB::commit();

            return $this->successResponse('OK', $user, 201);

        } catch (Exception $e) {
            DB::rollBack();
            Log::error('error register ' . $e->getMessage());
            
            return $this->errorResponse(null, 422, $e->getMessage());
        }
        
    }
}
