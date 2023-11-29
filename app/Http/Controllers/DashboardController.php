<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Attender;

class DashboardController extends Controller
{
    function summary() {
        try {
            $total_participants = (int) Attender::sum('participants');
            $total_will_attend = (int) Attender::where('attendance', 1)->count();
            $total_will_not_attend = (int) Attender::where('attendance', 2)->count();
            $total_post_comments = (int) Attender::where('status', 2)->count();
            $data = [
                'total_participants' => $total_participants == 1 || $total_participants == 0 ? $total_participants : $total_participants - 1,
                'total_will_attend' => $total_will_attend,
                'total_will_not_attend' => $total_will_not_attend,
                'total_post_comments' => $total_post_comments,
            ];

            return setRes($data, 200);
        } catch (\Exception $e) {
            return setRes(null, $e->getMessage() ? 400 : 500, $e->getMessage() ?? null);
        }
    }
}
