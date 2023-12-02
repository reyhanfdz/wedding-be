<?php
use Carbon\Carbon;
use Illuminate\Support\Facades\Mail;
use App\Mail\MailNotification;

if (!function_exists('setMeta')) {
    function setMeta($status, $message = null) {
        $result = [
            'status' => $status,
            'message' => $message,
        ];
        switch($status) {
            case 200:
                $result['message'] = $message ?? 'Ok';
                break;
            case 201:
                $result['message'] = $message ?? 'Created';
                break;
            case 400:
                $result['message'] = $message ?? 'Bad request - Please double check the data you sent';
                break;
            case 401:
                $result['message'] = $message ?? 'Unauthorized - This action need a token access';
                break;
            case 403:
                $result['message'] = $message ?? "Forbidden - You don't have an autorized for this action";
                break;
            case 404:
                $result['message'] = $message ?? "Data not found";
                break;
            case 405:
                $result['message'] = $message ?? "Method not allowed";
                break;
            default:
                $result['status'] = $status ?? 500;
                $result['message'] = $message ?? 'Somethins went wrong';
        }
        return $result;
    }
}

if (!function_exists('setRes')) {
    function setRes($data, $status, $message = null) {
        $resultMessage = setMeta($status, $message);
        $response = [
            'meta' => [
                'is_success' => $status >= 200 && $status <= 299 ? true : false,
                'status' => $resultMessage['status'],
                'message' => $resultMessage['message'],
            ],
            'data' => $data
        ];

        return response($response, $status);
    }
}

if (!function_exists('encryptToken')) {
    function encryptToken($data) {
        return Crypt::encrypt(json_encode($data));
    }
}

if (!function_exists('decryptToken')) {
    function decryptToken($data) {
        try {
            return json_decode(Crypt::decrypt($data));
        } catch (\Exception $e) {
            return 'error';
        }
    }
}

if (!function_exists('isTokenExpired')) {
    function isTokenExpired($valid_until) {
        $result = false;
        $user_valid_token = Carbon::parse($valid_until);
        $now = Carbon::now();
        if($user_valid_token < $now) $result = true;
        return $result;
    }
}

if (!function_exists('sendEmail')) {
    function sendEmail($data, $params) {
        $notification_params = [
            'subject' => $data['subject'],
            'to' => $data['to'],
            'view' => $data['view'],
            'params' => $params
        ];
        $notification_params['params']['subject'] = $data['subject'];
        Mail::send(new MailNotification($notification_params));
    }
}
