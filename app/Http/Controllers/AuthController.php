<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Hash;
use DB;
use Crypt;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use App\Models\User;

class AuthController extends Controller
{
    function login(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required'],
                'password' => ['required'],
            ], [
                'email.required' => 'Email is required',
                'password.required' => 'Password is required',
            ]);

            if ($validator->fails()) {
                DB::rollback();
                $errors = ['error' => [], 'errors' => []];
                if ($validator->errors()->has('email')) {
                    $errors['error']['email'] = $validator->errors()->first('email');
                    $errors['errors'][] = [
                        'field' => 'Email',
                        'message' => $validator->errors()->first('email'),
                    ];
                }
                if ($validator->errors()->has('password')) {
                    $errors['error']['password'] = $validator->errors()->first('password');
                    $errors['errors'][] = [
                        'field' => 'Password',
                        'message' => $validator->errors()->first('password'),
                    ];
                }
                return setRes($errors, 400);
            }

            $data = User::with(['profile'])->where('email', $request->email)->first();

            if ($data) {
                if ($data->status === 1) {
                    DB::rollback();
                    return setRes(null, 403, 'Your account is not active');
                }

                if (Hash::check($request->password, $data->password)) {
                    $date = Carbon::now();
                    $date->addDays(5);
                    $data['expired_until'] = $date;
                    $token = encryptToken($data);
                    $decrypt = decryptToken($token);
                    $data['access_token'] = $token;
                    unset($data['status']);
                    unset($data['expired_until']);
                    unset($data['profile']['user_id']);

                    $update_user = User::find($data->id);
                    $update_user->token = $token;
                    $update_user->save();

                    DB::commit();
                    return setRes($data, 200);
                }
            }

            DB::rollback();
            return setRes(null, 403, 'Email or Password does not match');
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }
}
