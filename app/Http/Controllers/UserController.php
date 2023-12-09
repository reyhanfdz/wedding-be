<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use App\Models\Profile;

class UserController extends Controller
{
    function list(Request $request) {
        try {
            $current_page = isset($request->page) ? $request->page : 1;
            $limit = isset($request->limit) ? $request->limit : 10;
            $keyword = isset($request->keyword) ? $request->keyword : null;
            $status = isset($request->status) ? $request->status : null;
            $role = isset($request->role) ? $request->role : null;
            $user = User::with('profile')
                ->when($status, function($query, $status) {
                    $query->where('status', $status);
                })
                ->when($role, function($query, $role) {
                    $query->where('role', $role);
                })
                ->when($keyword, function($query, $keyword) {
                    $query->where(function($query) use ($keyword) {
                        $query->where('email', 'like', '%'.$keyword.'%');
                        $query->orWhere('username', 'like', '%'.$keyword.'%');

                        $profile = Profile::where('name', 'like', '%'.$keyword.'%')->orWhere('phone', 'like', '%'.$keyword.'%')->get();
                        $query->orWhereIn('id', $profile);
                    });
                })
                ->orderBy('created_at', 'ASC')
                ->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page = $current_page);

            return setRes($user, 200);
        } catch (\Exception $e) {
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function create(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'regex:/^[a-zA-Z ]+$/'],
                'email' => ['required', 'unique:users', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/'],
                'phone' => ['required', 'unique:profiles', 'regex:/^[+]{1}(?:[0-9]\s?){6,15}[0-9]{1}$/'],
            ], [
                'name.required' => 'Name is required',
                'name.regex' => 'Name format is invalid, only alphabet and space',
                'email.required' => 'Email is required',
                'email.unique' => 'Email has been taken, try another email',
                'email.regex' => 'Email format is invalid',
                'phone.required' => 'Phone is required',
                'phone.unique' => 'Phone has been taken, try another phone number',
                'phone.regex' => 'Phone format is invalid, make sure to use country code like +62, min 6 char and max 15 char',
            ]);

            if ($validator->fails()) {
                if ($validator->errors()->has('name')) {
                    $errors['error']['name'] = $validator->errors()->first('name');
                    $errors['errors'][] = [
                        'field' => 'Name',
                        'message' => $validator->errors()->first('name'),
                    ];
                }

                if ($validator->errors()->has('email')) {
                    $errors['error']['email'] = $validator->errors()->first('email');
                    $errors['errors'][] = [
                        'field' => 'Email',
                        'message' => $validator->errors()->first('email'),
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

            $password = randomString(10, true);
            $user = User::create([
                'email' => $request->email,
                'password' => Hash::make($password),
                'status' => '1',
                'role' => 'staff',
            ]);
            Profile::create([
                'name' => $request->name,
                'phone' => $request->phone,
                'user_id' => $user->id
            ]);

            $data_email = [
                'subject' => 'No Reply | Create Account',
                'to' => $request->email,
                'view' => 'emails.create-account',
            ];
            $param_email = [
                'email' => $request->email,
                'password' => $password,
                'link' => env('APP_FE_URL').'activate-account?email='.$request->email
            ];
            sendEmail($data_email, $param_email);
            DB::commit();
            return setRes(null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function detail($id) {
        try {
            $data = User::with(['profile'])->find($id);

            if(!$data) {
                return setRes(null, 404);
            }

            return setRes($data, 200);
        } catch (\Exception $e) {
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function active($id) {
        DB::beginTransaction();
        try {
            $data = User::find($id);

            if(!$data) {
                DB::rollback();
                return setRes(null, 404);
            }

            if($data->status === User::$active) {
                DB::rollback();
                return setRes(null, 400, 'This user is active, cannot reactivate');
            }

            $data->token = null;
            $data->status = User::$active;
            $data->activate_token = null;
            $data->save();
            $data_email = [
                'subject' => 'No Reply | Account Activated',
                'to' => $data->email,
                'view' => 'emails.activate-account-validate-admin',
            ];
            $param_email = [
                'link' => env('APP_FE_URL')."login",
            ];
            sendEmail($data_email, $param_email);
            DB::commit();
            return setRes(null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function inactive($id) {
        DB::beginTransaction();
        try {
            $data = User::find($id);

            if(!$data) {
                DB::rollback();
                return setRes(null, 404);
            }

            if($data->status === User::$inactive) {
                DB::rollback();
                return setRes(null, 400, 'This user is inactive, cannot reinactivate');
            }

            $data->token = null;
            $data->status = User::$inactive;
            $data->activate_token = null;
            $data->save();

            $data_email = [
                'subject' => 'No Reply | Account Inactivated',
                'to' => $data->email,
                'view' => 'emails.inactivate-account-validate-admin',
            ];
            $param_email = [
                'link' => env('APP_FE_URL')."activate-account",
            ];
            sendEmail($data_email, $param_email);
            DB::commit();
            return setRes(null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function disable($id) {
        DB::beginTransaction();
        try {
            $data = User::find($id);

            if(!$data) {
                DB::rollback();
                return setRes(null, 404);
            }

            if($data->status === User::$disabled) {
                DB::rollback();
                return setRes(null, 400, 'This user is inactive, cannot reinactivate');
            }

            $data->token = null;
            $data->status = User::$disabled;
            $data->activate_token = null;
            $data->save();

            $data_email = [
                'subject' => 'No Reply | Account Disabled',
                'to' => $data->email,
                'view' => 'emails.disable-account-validate-admin',
            ];
            sendEmail($data_email, []);
            DB::commit();
            return setRes(null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function resetPassword($id) {
        DB::beginTransaction();
        try {
            $data = User::find($id);

            if(!$data) {
                DB::rollback();
                return setRes(null, 404);
            }

            $new_password = randomString(10, true);
            $data->token = null;
            $data->activate_token = null;
            $data->password = Hash::make($new_password);
            $data->save();

            $data_email = [
                'subject' => 'No Reply | Reset Password',
                'to' => $data->email,
                'view' => 'emails.reset-password-account-validate-admin',
            ];
            $params_email = [
                'link' => env('APP_FE_URL')."login",
                'password' => $new_password,
            ];
            sendEmail($data_email, $params_email);
            DB::commit();
            return setRes(null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function delete($id) {
        DB::beginTransaction();
        try {
            $data = User::with(['profile'])->find($id);

            if(!$data) {
                DB::rollback();
                return setRes(null, 404);
            }

            $data->profile->delete();
            $data->delete();
            DB::commit();
            return setRes(null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }
}
