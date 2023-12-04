<?php
use Illuminate\Http\Request;
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
        $valid_token = Carbon::parse($valid_until);
        $now = Carbon::now();
        if($valid_token < $now) $result = true;
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

if (!function_exists('randomString')) {
    function randomString($max, $withSpecialCharacter = false) {
        if($withSpecialCharacter) {
            $str_result = '!@#$%^&*0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        } else {
            $str_result = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXYZabcdefghijklmnopqrstuvwxyz';
        }
        return substr(str_shuffle($str_result), 0, $max);
    }
}

if (!function_exists('isBase64Valid')) {
    function isBase64Valid($data) {
        try {
            if (!preg_match('/^[a-zA-Z0-9\/\r\n+]*={0,2}$/', $data)) return false;
            $decoded = base64_decode($data, true);
            if(false === $decoded) return false;
            if(base64_encode($decoded) != $data) return false;
            return true;
        } catch (\Exception $e) {
            return false;
        }
    }
}

if (!function_exists('getUser')) {
    function getUser($token) {
        try {
            return decryptToken($token);
        } catch (\Exception $e) {
            return null;
        }
    }
}

if (!function_exists('getUserId')) {
    function getUserId($token) {
        try {
            return decryptToken($token)->id;
        } catch (\Exception $e) {
            return null;
        }
    }
}

if (!function_exists('getProfile')) {
    function getProfile($token) {
        try {
            return decryptToken($token)->profile;
        } catch (\Exception $e) {
            return null;
        }
    }
}

if (!function_exists('getProfileId')) {
    function getProfileId($token) {
        try {
            return decryptToken($token)->profile->id;
        } catch (\Exception $e) {
            return null;
        }
    }
}
