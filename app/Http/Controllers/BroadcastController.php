<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Excel;
// use Maatwebsite\Excel\Excel;
// use Maatwebsite\Excel\Excel as ExcelExcel;
use Illuminate\Support\Facades\Validator;
use App\Imports\BlockDomain;
use App\Imports\BroadcastsImport;
use App\Models\Broadcast;
use App\Models\Setting;

class BroadcastController extends Controller
{
    function list(Request $request) {
        try {
            $current_page = isset($request->page) ? $request->page : 1;
            $limit = isset($request->limit) ? $request->limit : 10;
            $keyword = isset($request->keyword) ? $request->keyword : null;
            $status_whatsapp = isset($request->status_whatsapp) && $request->status_whatsapp != 0 ? $request->status_whatsapp : null;
            $status_email = isset($request->status_email) && $request->status_email != 0 ? $request->status_email : null;

            $data = Broadcast::when($keyword, function($query, $keyword) {
                $query->where(function($query) use ($keyword) {
                    $query->where('name', 'like', '%'.$keyword.'%')
                        ->orWhere('whatsapp', 'like', '%'.$keyword.'%')
                        ->orWhere('email', 'like', '%'.$keyword.'%');
                });
            })->when($status_whatsapp, function($query, $status_whatsapp) {
                if ($status_whatsapp == 3) {
                    $query->whereNull('whatsapp');
                } else {
                    $query->where(function($query) use ($status_whatsapp) {
                        $query->whereNotNull('whatsapp')
                            ->where('status_whatsapp', $status_whatsapp);
                    });
                }
            })->when($status_email, function($query, $status_email) {
                if ($status_email == 3) {
                    $query->whereNull('email');
                } else {
                    $query->where(function($query) use ($status_email) {
                        $query->whereNotNull('email')
                            ->where('status_email', $status_email);
                    });
                }
            })->orderBy('updated_at', 'DESC')
            ->paginate($perPage = $limit, $columns = ['*'], $pageName = 'page', $page = $current_page);

            return setRes($data, 200);
        } catch (\Exception $e) {
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function import(Request $request) {
        try {
            $validator = Validator::make($request->all(), [
                'excel' => ['required', 'mimes:xlsx,xls'],
            ], [
                'excel.required' => 'Excel is required',
                'excel.mimes' => 'Excel format file is invalid, only xlsx or xls',
            ]);

            if ($validator->fails()) {
                $errors = ['error' => [], 'errors' => []];
                if ($validator->errors()->has('excel')) {
                    $errors['error']['excel'] = $validator->errors()->first('excel');
                    $errors['errors'][] = [
                        'field' => 'Excel',
                        'message' => $validator->errors()->first('excel'),
                    ];
                }
                DB::rollback();
                return setRes($errors, 400);
            }

            $broadcast_import = new BroadcastsImport;
            Excel::import($broadcast_import , $request->excel);

            $result = $broadcast_import->getResult();
            if (count($result["error_messages"]) > 0) {
                $errors = ['error' => [], 'errors' => []];
                foreach($result["error_messages"] as $item) {
                    $lowerField = strtolower($item['field']);
                    if (!isset($errors['error'][$lowerField])) $errors['error'][$lowerField] = $item['message'];
                    $errors['errors'][] = [
                        'field' => $item['field'],
                        'message' => $item['message'],
                    ];
                }
                DB::rollback();
                return setRes($errors, 400);
            }

            DB::commit();
            return setRes(null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function create(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'max:50', 'regex:/^[A-Za-z\s]*$/'],
                'whatsapp' => ['nullable', 'unique:broadcasts', 'regex:/^[+]{1}(?:[0-9]\s?){6,15}[0-9]{1}$/'],
                'email' => ['nullable', 'unique:broadcasts', 'max:50', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/'],
            ], [
                'name.required' => 'Name is required',
                'name.max' => 'Name minimum 6 character',
                'name.regex' => 'Name format is invalid, only alphabet and space',
                'whatsapp.regex' => 'Whatsapp format is invalid, make sure to use country code like +62, min 6 char and max 15 char',
                'whatsapp.unique' => 'Whatsapp number has been taken',
                'email.regex' => 'Email format is invalid',
                'email.unique' => 'Email has been taken',
            ]);

            if ($validator->fails()) {
                $errors = ['error' => [], 'errors' => []];
                if ($validator->errors()->has('name')) {
                    $errors['error']['name'] = $validator->errors()->first('name');
                    $errors['errors'][] = [
                        'field' => 'Name',
                        'message' => $validator->errors()->first('name'),
                    ];
                }

                if ($validator->errors()->has('whatsapp')) {
                    $errors['error']['whatsapp'] = $validator->errors()->first('whatsapp');
                    $errors['errors'][] = [
                        'field' => 'Whatsapp',
                        'message' => $validator->errors()->first('whatsapp'),
                    ];
                }

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

            if (!$request->whatsapp && !$request->email) {
                $errors['error']['contact'] = 'Field whatsapp and email cannot null at sametime';
                $errors['errors'][] = [
                    'field' => 'Contact',
                    'message' => 'Field whatsapp and email cannot null at sametime',
                ];
                DB::rollback();
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

            $data['name'] = $request->name;
            if ($request->whatsapp) $data['whatsapp'] = $request->whatsapp;
            if ($request->email) $data['email'] = $request->email;
            Broadcast::create($data);

            DB::commit();
            return setRes(null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function detail($id) {
        try {
            $data = Broadcast::find($id);
            if(!$data) {
                return setRes(null, 404);
            }

            return setRes($data, 200);
        } catch (\Exception $e) {
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function update(Request $request, $id) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => ['required', 'max:50', 'regex:/^[A-Za-z\s]*$/'],
                'whatsapp' => ['nullable', 'unique:broadcasts,whatsapp,'.$id.',id', 'regex:/^[+]{1}(?:[0-9]\s?){6,15}[0-9]{1}$/'],
                'email' => ['nullable', 'unique:broadcasts,email,'.$id.',id', 'max:50', 'regex:/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/'],
            ], [
                'name.required' => 'Name is required',
                'name.max' => 'Name minimum 6 character',
                'name.regex' => 'Name format is invalid, only alphabet and space',
                'whatsapp.regex' => 'Whatsapp format is invalid, make sure to use country code like +62, min 6 char and max 15 char',
                'whatsapp.unique' => 'Whatsapp number has been taken',
                'email.regex' => 'Email format is invalid',
                'email.unique' => 'Email has been taken',
            ]);

            if ($validator->fails()) {
                $errors = ['error' => [], 'errors' => []];
                if ($validator->errors()->has('name')) {
                    $errors['error']['name'] = $validator->errors()->first('name');
                    $errors['errors'][] = [
                        'field' => 'Name',
                        'message' => $validator->errors()->first('name'),
                    ];
                }

                if ($validator->errors()->has('whatsapp')) {
                    $errors['error']['whatsapp'] = $validator->errors()->first('whatsapp');
                    $errors['errors'][] = [
                        'field' => 'Whatsapp',
                        'message' => $validator->errors()->first('whatsapp'),
                    ];
                }

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

            if (!$request->whatsapp && !$request->email) {
                $errors['error']['contact'] = 'Field whatsapp and email cannot null at sametime';
                $errors['errors'][] = [
                    'field' => 'Contact',
                    'message' => 'Field whatsapp and email cannot null at sametime',
                ];
                DB::rollback();
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

            $data = Broadcast::find($id);

            if(!$data) {
                DB::rollback();
                return setRes(null, 404);
            }

            $data->name = $request->name;
            $data->whatsapp = $request->whatsapp??null;
            $data->email = $request->email??null;
            $data->save();
            DB::commit();
            return setRes(null, 201);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function sendToWhatsapp($id) {
        DB::beginTransaction();
        try {
            $data = Broadcast::find($id);
            if(!$data) {
                DB::rollback();
                return setRes(null, 404);
            }

            if(!$data->whatsapp) {
                DB::rollback();
                return setRes(null, 404);
            }

            $setting = Setting::find(1);
            if (!$setting) {
                DB::rollback();
                return setRes(null, 400, 'Setting data is not created yet');
            }

            $data->status_whatsapp = Broadcast::$whatsapp_sent;
            $data->save();

            $name = str_replace(' ', '__', $data->name);
            $link_fe = env("APP_FE_URL").'?name='.$name;
            $invitaion_message = 'Assalamualaikum Warahmatullahi Wabarakatuh%0a%0aTanpa mengurangi rasa hormat, perkenankan kami mengundang Bapak/Ibu/Saudara/i, untuk menghadiri acara pernikahan kami, pada:%0a- Tanggal: '.indonesianDate($setting->event_ceremonial_date).'%0a- ⁠Lokasi: '.$setting->event_ceremonial_address.'%0a%0aSuatu kebahagiaan bagi kami apabila Bapak/Ibu/Saudara/i berkenan untuk hadir dan memberikan doa restu.%0a%0aTerima kasih banyak atas perhatiannya.%0a%0aWassalamualaikum Warahmatullahi Wabarakatuh%0a%0a~ _Reyhan dan Murni_%0a%0aInfo lebih lanjut dan undangan pernikahan:%0a'.$link_fe;
            $res_data = [
                'invitation_message' => $invitaion_message,
                'link' => Broadcast::$link_whatsapp.'?phone='.$data->whatsapp.'&text='.$invitaion_message,
            ];

            DB::commit();
            return setRes($res_data, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }


    function sendToEmail($id) {
        DB::beginTransaction();
        try {
            $data = Broadcast::find($id);
            if(!$data) {
                DB::rollback();
                return setRes(null, 404);
            }

            if(!$data->email) {
                DB::rollback();
                return setRes(null, 404);
            }

            $setting = Setting::find(1);
            if (!$setting) {
                DB::rollback();
                return setRes(null, 400, 'Setting data is not created yet');
            }

            $data->status_email = Broadcast::$email_sent;
            $data->save();

            $data_email = [
                'subject' => 'No Reply | Undangan Pernikahan',
                'to' => $data->email,
                'view' => 'emails.invitation',
            ];
            $name = str_replace(' ', '__', $data->name);
            $param_email = [
                'link' => env("APP_FE_URL")."?name=".$name,
                'from' => env("MAIL_FROM_NAME"),
                'ceremonial' => [
                    'date' => $setting->event_ceremonial_date ? indonesianDate($setting->event_ceremonial_date) : '-',
                    'start_time' => $setting->event_ceremonial_start_time??'-',
                    'end_time' => $setting->event_ceremonial_end_time??'Selesai',
                    'link' => $setting->event_ceremonial_link??'#',
                    'address' => $setting->event_ceremonial_address??'-',
                ],
                'party' => [
                    'date' => $setting->event_party_date ? indonesianDate($setting->event_party_date) : '-',
                    'start_time' => $setting->event_party_start_time??'-',
                    'end_time' => $setting->event_party_end_time??'Selesai',
                    'link' => $setting->event_party_link??'#',
                    'address' => $setting->event_party_address??'-',
                ],
                'traditional' => [
                    'date' => $setting->event_traditional_date ? indonesianDate($setting->event_traditional_date) : '-',
                    'start_time' => $setting->event_traditional_start_time??'-',
                    'end_time' => $setting->event_traditional_end_time??'Selesai',
                    'link' => $setting->event_traditional_link??'#',
                    'address' => $setting->event_traditional_address??'-',
                ]
            ];
            sendEmail($data_email, $param_email);
            DB::commit();
            return setRes(null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function remove($id) {
        DB::beginTransaction();
        try {
            $data = Broadcast::find($id);
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
}
