<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Profile;

class ProfileController extends Controller
{
    function get(Request $request) {
        try {
            $id = decryptToken($request->header('Authorization'))->id;

            $data = User::with('profile')->find($id);
            if(!$data) {
                return setRes(null, 404, 'User not found');
            }

            unset($data['status']);
            unset($data['profile']['user_id']);
            return setRes($data, 200);
        } catch (\Exception $e) {
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function update(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required'],
                'phone' => ['required'],
            ], [
                'name.required' => 'Name is required',
                'phone.required' => 'Phone is required',
            ]);

            if ($validator->fails()) {
                if ($validator->errors()->has('name')) {
                    $errors['error']['name'] = $validator->errors()->first('name');
                    $errors['errors'][] = [
                        'field' => 'Name',
                        'message' => $validator->errors()->first('name'),
                    ];
                }

                if ($validator->errors()->has('phone')) {
                    $errors['error']['phone'] = $validator->errors()->first('phone');
                    $errors['errors'][] = [
                        'field' => 'Phone',
                        'message' => $validator->errors()->first('phone'),
                    ];
                }
                DB::rollback();
                return setRes($errors, 400);
            }

            $id = decryptToken($request->header('Authorization'))->profile->id;
            $data = Profile::find($id);

            if(!$data) {
                DB::rollback();
                return setRes(null, 404, 'User not found');
            }

            $data->name = $request->name;
            $data->phone = $request->phone;
            if (isBase64Valid($request->image)) $data->image = $request->image;
            $data->save();

            DB::commit();
            return setRes(null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function changePassword(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'current_password' => ['required'],
                'new_password' => ['required', 'regex:/^(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[a-zA-Z])[a-zA-Z0-9!@#$%^&*]+$/'],
            ], [
                'current_password.required' => 'Current password is required',
                'new_password.required' => 'New password is required',
                'new_password.regex' => 'New Password format is invalid (must be containt alphabet, numeric and special character)',
            ]);

            if ($validator->fails()) {
                if ($validator->errors()->has('current_password')) {
                    $errors['error']['current_password'] = $validator->errors()->first('current_password');
                    $errors['errors'][] = [
                        'field' => 'Current Password',
                        'message' => $validator->errors()->first('current_password'),
                    ];
                }

                if ($validator->errors()->has('new_password')) {
                    $errors['error']['new_password'] = $validator->errors()->first('new_password');
                    $errors['errors'][] = [
                        'field' => 'New Password',
                        'message' => $validator->errors()->first('new_password'),
                    ];
                }
                DB::rollback();
                return setRes($errors, 400);
            }

            $id = decryptToken($request->header('Authorization'))->profile->id;
            $data = Profile::with('user')->find($id);

            if(!$data) {
                DB::rollback();
                return setRes(null, 404, 'Profile not found');
            }

            if (!Hash::check($request->current_password, $data->user->password)) {
                DB::rollback();
                return setRes(null, 400, 'Current password does not match with your account');
            }

            if ($request->current_password == $request->new_password) {
                DB::rollback();
                return setRes(null, 400, 'Password and new password must be different');
            }

            $user = $data->user;
            $user->password = Hash::make($request->new_password);
            $user->save();
            DB::commit();
            return setRes(null, 200);
        } catch (\Exception $e) {
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function checkUsername(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'username' => ['required', 'regex:/^[a-z0-9]+$/'],
            ], [
                'username.required' => 'Username is required',
                'username.regex' => 'Username format is invalid (only lowercase alphabet and numeric)',
            ]);

            if ($validator->fails()) {
                if ($validator->errors()->has('username')) {
                    $errors['error']['username'] = $validator->errors()->first('username');
                    $errors['errors'][] = [
                        'field' => 'Username',
                        'message' => $validator->errors()->first('username'),
                    ];
                }

                DB::rollback();
                return setRes($errors, 400);
            }

            $id = decryptToken($request->header('Authorization'))->profile->id;
            $data = Profile::with('user')->find($id);

            if (!$data) {
                DB::rollback();
                return setRes(null, 404);
            }

            $user = $data->user;
            if($user->username) {
                DB::rollback();
                return setRes(null, 400, 'Your account has been set a username before, cannot check available username');
            }

            $check_username = User::where('username', $request->username)->first();
            if($check_username) {
                DB::rollback();
                return setRes(null, 400, 'Username is not available, try another one');
            }

            return setRes(null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function changeUsername(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'username' => ['required', 'regex:/^[a-z0-9]+$/'],
            ], [
                'username.required' => 'Username is required',
                'username.regex' => 'Username format is invalid (only lowercase alphabet and numeric)',
            ]);

            if ($validator->fails()) {
                if ($validator->errors()->has('username')) {
                    $errors['error']['username'] = $validator->errors()->first('username');
                    $errors['errors'][] = [
                        'field' => 'Username',
                        'message' => $validator->errors()->first('username'),
                    ];
                }

                DB::rollback();
                return setRes($errors, 400);
            }

            $id = decryptToken($request->header('Authorization'))->profile->id;
            $data = Profile::with('user')->find($id);

            if (!$data) {
                DB::rollback();
                return setRes(null, 404);
            }

            $user = $data->user;
            if($user->username) {
                DB::rollback();
                return setRes(null, 400, 'Your account has been set a username before, cannot change current username');
            }

            $check_username = User::where('username', $request->username)->first();
            if($check_username) {
                DB::rollback();
                return setRes(null, 400, 'Username has been taken, try another one');
            }

            $user->username = $request->username;
            $user->save();
            DB::commit();
            return setRes(null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }
}
