<?php

if (!function_exists('setMeta')) {
    function setMeta($status, $message = null) {
        $result = [
            'status' => $status,
            'message' => $message,
        ];
        switch($status) {
            case 200:
                $result['message'] = $message ?? 'Success';
                break;
            case 201:
                $result['message'] = $message ?? 'Success send data';
                break;
            case 400:
                $result['message'] = $message ?? 'Bad request - Please double check the data you sent';
                break;
            case 403:
                $result['message'] = $message ?? "You don't have autorized for this action";
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
        return json_decode(Crypt::decrypt($data));
    }
}
