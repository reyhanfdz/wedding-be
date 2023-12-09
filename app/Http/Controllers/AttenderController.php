<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Attender;
use App\Models\BlockDomain;

class AttenderController extends Controller
{
    function list(Request $request) {
        try {
            $current_page = isset($request->page) ? $request->page : 1;
            $limit = isset($request->limit) ? $request->limit : 1;
            $keyword = isset($request->keyword) ? $request->keyword : null;
            $attendance = isset($request->attendance) && $request->attendance != 0 ? $request->attendance : null;
            $status = isset($request->status) && $request->status != 0 ? $request->status : null;
            $status_attend = isset($request->status_attend) && $request->status_attend != 0 ? $request->status_attend : null;
            $data = Attender::when($keyword, function($query, $keyword) {
                $query->where(function($q) use ($keyword) {
                    $q->where('name', 'like', '%'.$keyword.'%')->orWhere('email', 'like', '%'.$keyword.'%');
                });
            })->when($attendance, function($query, $attendance) {
                $query->where('attendance', (int) $attendance);
            })->when($status, function($query, $status) {
                $query->where('status', (int) $status);
            })->when($status_attend, function($query, $status_attend) {
                $query->where('status_attend', (int) $status_attend);
            })
            ->orderBy('updated_at', 'DESC')
            ->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page = $current_page);

            return setRes($data, 200);
        } catch (\Exception $e) {
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function create(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'max:50', 'regex:/^[A-Za-z\s]*$/'],
                'email' => ['required', 'unique:attenders', 'max:50', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/'],
                'participants' => ['required'],
                'attendance' => ['required'],
                'comment' => ['required', 'max:230'],
            ], [
                'name.required' => 'Name is required',
                'name.max' => 'Name max 50 character',
                'name.regex' => 'Name format is invalid (only alphabet and space)',
                'email.required' => 'Email is required',
                'email.unique' => 'Email filled, please try another email',
                'email.max' => 'Email max 50 character',
                'email.regex' => 'Email format is invalid',
                'participants.required' => 'Participants is required',
                'attendance.required' => 'Attendance is required',
                'comment.required' => 'Comment is required',
                'comment.max' => 'Comment max 230 character',
            ]);

            if ($validator->fails()) {
                DB::rollback();
                $errors = ['error' => [], 'errors' => []];
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
                if ($validator->errors()->has('participants')) {
                    $errors['error']['participants'] = $validator->errors()->first('participants');
                    $errors['errors'][] = [
                        'field' => 'Participants',
                        'message' => $validator->errors()->first('participants'),
                    ];
                }
                if ($validator->errors()->has('attendance')) {
                    $errors['error']['attendance'] = $validator->errors()->first('attendance');
                    $errors['errors'][] = [
                        'field' => 'Attendance',
                        'message' => $validator->errors()->first('attendance'),
                    ];
                }
                if ($validator->errors()->has('comment')) {
                    $errors['error']['comment'] = $validator->errors()->first('comment');
                    $errors['errors'][] = [
                        'field' => 'Comment',
                        'message' => $validator->errors()->first('comment'),
                    ];
                }
                return setRes($errors, 400);
            }

            $mode = env("APP_ENV");
            if ($mode === 'production') {
                $email_domain = explode('@',$request->email);
                $email_domain = explode('.',end($email_domain));
                $email_domain = $email_domain[0];
                $block_domain = BlockDomain::where('name', 'like','%'.$email_domain.'%')->first();
                if($block_domain) {
                    $result = [
                        "error" => [
                            "email" => "Email domain (".$email_domain.") is not allowed, try another email"
                        ],
                        "errors" => [
                            [
                                "field" => "Email",
                                "message" => "Email domain (".$email_domain.") is not allowed, try another email"
                            ]
                        ]
                    ];
                    return setRes($result, 400);
                }
            }

            $data = Attender::create([
                'name' => $request->name,
                'email' => $request->email,
                'participants' => $request->participants,
                'attendance' => $request->attendance,
                'status' => 1,
                'comment' => $request->comment,
            ]);
            unset($data['link_qr']);
            $token = encryptToken($data);
            $link = Attender::$qr_url.$token;
            $data->link_qr = $token;
            $data->save();

            $data_email = [
                'subject' => 'No Reply | Submit Reservation',
                'to' => $data->email,
                'view' => 'emails.qr',
            ];
            $param_email = ['link_qr' => $link];
            sendEmail($data_email, $param_email);

            DB::commit();
            return setRes(['link_qr' => $link], 201);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function detail($id) {
        try {
            $data = Attender::find($id);

            if(!$data) {
                return setRes(null, 404);
            }

            return setRes($data, 200);
        } catch (\Exception $e) {
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function activeStatus($id) {
        DB::beginTransaction();
        try {
            $data = Attender::find($id);

            if(!$data) {
                DB::rollback();
                return setRes(null, 404);
            }

            if($data->status == Attender::$comment_displayed) {
                DB::rollback();
                return setRes(null, 400, "This attenders is currently active, can't set to active");
            }

            $data->status = Attender::$comment_displayed;
            $data->save();
            DB::commit();
            return setRes(null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function inactiveStatus($id) {
        DB::beginTransaction();
        try {
            $data = Attender::find($id);

            if(!$data) {
                DB::rollback();
                return setRes(null, 404);
            }

            if($data->status == Attender::$comment_not_displayed) {
                DB::rollback();
                return setRes(null, 400, "This attenders is currently inactive, can't set to inactive");
            }

            $data->status = Attender::$comment_not_displayed;
            $data->save();
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
            $data = Attender::find($id);

            if(!$data) {
                DB::rollback();
                return setRes(null, 404);
            }

            $data->delete();
            DB::commit();
            return setRes(null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function attend(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'token' => ['required'],
            ], [
                'token.required' => 'Token is required',
            ]);

            if ($validator->fails()) {
                DB::rollback();
                $errors = ['error' => [], 'errors' => []];
                if ($validator->errors()->has('token')) {
                    $errors['error']['token'] = $validator->errors()->first('token');
                    $errors['errors'][] = [
                        'field' => 'Token',
                        'message' => $validator->errors()->first('token'),
                    ];
                }
                return setRes($errors, 400);
            }

            $result = decryptToken($request->token);
            if($result === 'error') {
                DB::rollback();
                return setRes(null, 400, 'Invalid token QR');
            }

            $data = Attender::find($result->id);
            if(!$data) {
                DB::rollback();
                return setRes(null, 400, 'Invalid token QR, does not match with any data');
            }
            if($data->link_qr !== $request->token) {
                DB::rollback();
                return setRes(null, 400, 'Invalid token QR, You may has request regenarate new QR before');
            }
            if((int) $data->status_attend === Attender::$qr_scaned) {
                DB::rollback();
                return setRes(null, 400, 'You have attend before, cannot attend with the same data');
            }

            $data->status_attend = Attender::$qr_scaned;
            $data->link_qr = null;
            $data->save();

            $data_email = [
                'subject' => 'No Reply | Attend',
                'to' => $data->email,
                'view' => 'emails.scan-qr',
            ];
            $param_email = ['link_qr' => $link];
            sendEmail($data_email, $param_email);

            DB::commit();
            unset($data['status_attend']);
            return setRes($data, 201);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function getDisplayedComment() {
        try {
            $data = Attender::where('status', 2)->orderBy('created_at', 'ASC')->get();

            return setRes($data, 200);
        } catch (\Exception $e) {
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function generateNewQr($id) {
        DB::beginTransaction();
        try {
            $data = Attender::find($id);
            if(!$data) {
                DB::rollback();
                return setRes(null, 400);
            }

            $data->link_qr = null;
            unset($data['link_qr']);
            $token = encryptToken($data);
            $link = Attender::$qr_url.$token;
            $data->link_qr = $token;
            $data->save();

            $data_email = [
                'subject' => 'No Reply | Regenerate QR Code',
                'to' => $data->email,
                'view' => 'emails.refresh-qr',
            ];
            $param_email = ['link_qr' => $link];
            sendEmail($data_email, $param_email);

            DB::commit();
            return setRes(null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }
}
