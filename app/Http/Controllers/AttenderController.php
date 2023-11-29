<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Attender;

class AttenderController extends Controller
{
    function create(Request $request) {
        DB::beginTransaction();
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required',
                'email' => 'required',
                'participants' => 'required',
                'attendance' => 'required',
                'comment' => 'required',
            ], [
                'name.required' => 'Name is required',
                'email.required' => 'Email is required',
                'participants.required' => 'Participants is required',
                'attendance.required' => 'Attendance is required',
                'comment.required' => 'Comment is required',
            ]);

            if ($validator->fails()) {
                DB::rollback();
                $errors = ['errors' => []];
                if ($validator->errors()->has('name')) $errors['errors']['name'] = $validator->errors()->first('name');
                if ($validator->errors()->has('email')) $errors['errors']['email'] = $validator->errors()->first('email');
                if ($validator->errors()->has('participants')) $errors['errors']['participants'] = $validator->errors()->first('participants');
                if ($validator->errors()->has('attendance')) $errors['errors']['attendance'] = $validator->errors()->first('attendance');
                if ($validator->errors()->has('comment')) $errors['errors']['comment'] = $validator->errors()->first('comment');
                return setRes($errors, 400);
            }

            $data = Attender::create([
                'name' => $request->name,
                'email' => $request->email,
                'participants' => $request->participants,
                'attendance' => $request->attendance,
                'status' => 1,
                'comment' => $request->comment,
            ]);

            DB::commit();
            return setRes(null, 201);
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

            if($data->status == 2) {
                DB::rollback();
                return setRes(null, 400, "This attenders is currently active, can't set to active");
            }

            $data->status = 2;
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

            if($data->status == 1) {
                DB::rollback();
                return setRes(null, 400, "This attenders is currently inactive, can't set to inactive");
            }

            $data->status = 1;
            $data->save();
            DB::commit();
            return setRes(null, 200);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }
}
