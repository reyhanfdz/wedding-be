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
                DB::rollback();
                return setRes($errors, 400);
            }

            $data = User::with(['profile'])->where('email', $request->email)->first();

            if (!$data) {
                DB::rollback();
                return setRes(null, 400, 'Email or Password does not match');
            }

            if ($data->status === 1) {
                DB::rollback();
                return setRes(null, 400, 'Your account is not active yet');
            }

            if ($data->status === 3) {
                DB::rollback();
                return setRes(null, 400, 'Your account is disabled by admin, contact admin to enable your account (email:'.env('EMAIL_ADMIN').', whatsapp:'.env('PHONE_WHATSAPP').')');
            }

            if (!Hash::check($request->password, $data->password)) {
                DB::rollback();
                return setRes(null, 400, 'Email or Password does not match');
            }

            $date = Carbon::now();
            $date->addDays(5);
            $data['expired_until'] = $date;
            $token = encryptToken($data);
            $data['access_token'] = $token;
            unset($data['status']);
            unset($data['expired_until']);
            unset($data['profile']['user_id']);

            $update_user = User::find($data->id);
            $update_user->token = $token;
            $update_user->save();

            DB::commit();
            return setRes($data, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function forgotPassword(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required'],
            ], [
                'email.required' => 'Email is required',
            ]);

            if ($validator->fails()) {
                $errors = ['error' => [], 'errors' => []];
                if ($validator->errors()->has('email')) {
                    $errors['error']['email'] = $validator->errors()->first('email');
                    $errors['errors'][] = [
                        'field' => 'Email',
                        'message' => $validator->errors()->first('email'),
                    ];
                }
                DB::rollback();
                return setRes($errors, 400);
            }

            $data = User::where('email', $request->email)->first();

            if(!$data) {
                DB::rollback();
                return setRes(null, 404, "We can't find your account make sure you fill the correct email");
            }

            $date = Carbon::now();
            $date->addMinutes(30);
            $enc_data = [
                'id' => $data->id,
                'expired_until' => $date,
            ];
            $token = encryptToken($enc_data);
            $data->forgot_token = $token;
            $data->save();

            $data_email = [
                'subject' => 'No Reply | Forgot Password',
                'to' => $data->email,
                'view' => 'emails.forgot-password',
            ];
            $param_email = [
                'link_forgot' => env('APP_FE_URL')."reset-password/".$token,
                'valid_token_date' => Carbon::parse($date)->format('Y-m-d H:i:s'),
            ];
            sendEmail($data_email, $param_email);
            DB::commit();
            return setRes(null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function resetPassword(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'token' => ['required'],
                'password' => ['required', 'regex:/^(?=.*[0-9])(?=.*[!@#$%^&*])(?=.*[a-zA-Z])[a-zA-Z0-9!@#$%^&*]+$/'],
            ], [
                'token.required' => 'Token is required',
                'password.required' => 'Password is required',
                'password.regex' => 'Password format is invalid (must be containt alphabet, numeric and special character)',
            ]);

            if ($validator->fails()) {
                $errors = ['error' => [], 'errors' => []];
                if ($validator->errors()->has('token')) {
                    $errors['error']['token'] = $validator->errors()->first('token');
                    $errors['errors'][] = [
                        'field' => 'Token',
                        'message' => $validator->errors()->first('token'),
                    ];
                }

                if ($validator->errors()->has('password')) {
                    $errors['error']['password'] = $validator->errors()->first('password');
                    $errors['errors'][] = [
                        'field' => 'Password',
                        'message' => $validator->errors()->first('password'),
                    ];
                }
                DB::rollback();
                return setRes($errors, 400);
            }

            $decode_token = decryptToken($request->token);
            if($decode_token == 'error') {
                DB::rollback();
                return setRes($errors, 400, "Invalid token reset password");
            }

            $isExpired = isTokenExpired($decode_token->expired_until);
            if($isExpired) {
                DB::rollback();
                return setRes(null, 400, 'Token reset password has expired');
            }

            $data_token = User::where('forgot_token', $request->token)->first();

            if(!$data_token) {
                DB::rollback();
                return setRes(null, 404, "Invalid token reset password");
            }

            $data = User::find($decode_token->id);
            if(!$data) {
                DB::rollback();
                return setRes(null, 400, "User not found");
            }

            $data->password = Hash::make($request->password);
            $data->token = null;
            $data->forgot_token = null;
            $data->save();

            $data_email = [
                'subject' => 'No Reply | Reset Password',
                'to' => $data->email,
                'view' => 'emails.reset-password',
            ];
            $param_email = [
                'link_login' => env('APP_FE_URL')."login",
            ];
            sendEmail($data_email, $param_email);
            DB::commit();
            return setRes(null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function activateAccount(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'email' => ['required'],
            ], [
                'email.required' => 'Email is required',
            ]);

            if ($validator->fails()) {
                $errors = ['error' => [], 'errors' => []];
                if ($validator->errors()->has('email')) {
                    $errors['error']['email'] = $validator->errors()->first('email');
                    $errors['errors'][] = [
                        'field' => 'Email',
                        'message' => $validator->errors()->first('email'),
                    ];
                }
                DB::rollback();
                return setRes($errors, 400);
            }

            $data = User::where('email', $request->email)->first();

            if(!$data) {
                DB::rollback();
                return setRes(null, 404, "We can't find your account make sure you fill the correct email");
            }

            if($data->status == 2) {
                DB::rollback();
                return setRes(null, 404, "Your account is active, can not reactive");
            }

            if($data->status == 3) {
                DB::rollback();
                return setRes(null, 404, "Your account is disabled by admin, contact admin to open your account (email:".env("EMAIL_ADMIN").', whatsapp: '.env("PHONE_WHATSAPP").")");
            }

            $date = Carbon::now();
            $date->addMinutes(30);
            $enc_data = [
                'id' => $data->id,
                'expired_until' => $date,
            ];
            $token = encryptToken($enc_data);
            $data->activate_token = $token;
            $data->save();

            $data_email = [
                'subject' => 'No Reply | Acivate Account',
                'to' => $data->email,
                'view' => 'emails.activate-account',
            ];
            $param_email = [
                'link' => env('APP_FE_URL')."activate-account/".$token,
                'valid_token_date' => Carbon::parse($date)->format('Y-m-d H:i:s'),
            ];
            sendEmail($data_email, $param_email);
            DB::commit();
            return setRes(null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function activateAccountValidate(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'token' => ['required'],
            ], [
                'token.required' => 'Token is required',
            ]);

            if ($validator->fails()) {
                $errors = ['error' => [], 'errors' => []];
                if ($validator->errors()->has('token')) {
                    $errors['error']['token'] = $validator->errors()->first('token');
                    $errors['errors'][] = [
                        'field' => 'Token',
                        'message' => $validator->errors()->first('token'),
                    ];
                }
                DB::rollback();
                return setRes($errors, 400);
            }

            $decode_token = decryptToken($request->token);
            if($decode_token == 'error') {
                DB::rollback();
                return setRes($errors, 400, "Invalid token activate account");
            }

            $isExpired = isTokenExpired($decode_token->expired_until);
            if($isExpired) {
                DB::rollback();
                return setRes(null, 400, 'Token activate account has expired');
            }

            $data_token = User::where('activate_token', $request->token)->first();

            if(!$data_token) {
                DB::rollback();
                return setRes(null, 400, "Invalid token activate account");
            }

            $data = User::find($decode_token->id);

            if(!$data) {
                DB::rollback();
                return setRes(null, 404, "User not found");
            }

            $data->status = 2;
            $data->token = null;
            $data->activate_token = null;
            $data->save();

            $data_email = [
                'subject' => 'No Reply | Account Activated',
                'to' => $data->email,
                'view' => 'emails.activate-account-validate',
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
}
