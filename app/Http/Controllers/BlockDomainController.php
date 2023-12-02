<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Validator;
use App\Models\BlockDomain;

class BlockDomainController extends Controller
{
    function list(Request $request) {
        try {
            $current_page = isset($request->page) ? $request->page : 1;
            $limit = isset($request->limit) ? $request->limit : 1;
            $keyword = isset($request->keyword) ? $request->keyword : null;
            $data = BlockDomain::when($keyword, function($query, $keyword) {
                $query->where('name', 'like', '%'.$keyword.'%');
            })
            ->orderBy('created_at', 'DESC')
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
                'name' => ['required', 'max:50', 'unique:block_domains'],
            ], [
                'name.required' => 'Name is required',
                'name.max' => 'Name max 50 character',
                'name.unique' => 'Email domain filled, please try another email domain',
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
                return setRes($errors, 400);
            }

            $data = BlockDomain::create(['name' => $request->name]);
            DB::commit();
            return setRes(null, 201);
        } catch (\Exception $e) {
            DB::rollback();
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }

    function delete($id) {
        DB::beginTransaction();
        try {
            $data = BlockDomain::find($id);

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
