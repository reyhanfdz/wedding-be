<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use App\Models\Broadcast;

class BroadcastsImport implements ToCollection
{
    /**
    * @param Collection $collection
    */

    private $message = [];
    private $successCount = 0;
    private $key = null;

    public function collection(Collection $rows)
    {
        if (count($rows) > 1) {
            if (count ($rows) > 101) {
                $this->message[] = [
                    'field' => 'Excel',
                    'message' => 'Excel max 100 rows data',
                ];
            } else {
                foreach ($rows as $key => $row) {
                    if ($key > 0) {
                        $this->key = $key;
                        $name = $row[1];
                        $whatsapp = $row[2];
                        $email = $row[3];

                        if (!$whatsapp && !$email) {
                            $this->message[] = [
                                'field' => 'Contact',
                                'message' => "Row ".$key.', Field whatsapp and email cannot null at sametime',
                            ];
                        } else {
                            $name_regex = '/^[A-Za-z\s]*$/';
                            $phone_regex = '/^[+]{1}(?:[0-9]\s?){6,15}[0-9]{1}$/';
                            $email_regex = '/^(([^<>()[\]\\.,;:\s@"]+(\.[^<>()[\]\\.,;:\s@"]+)*)|.(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/';

                            $validate_name = $this->validate($name, "Name", $name_regex, "format is invalid, only alphabet and space");
                            $validate_whtasapp = $this->validate($whatsapp, "Whatsapp", $phone_regex, "format is invalid, make sure to use country code like +62, min 6 char and max 15 char");
                            $validate_email = $this->validate($email, "Email", $email_regex, "format is invalid");
                        }

                        $broadcast = [];
                        if ($validate_name && $validate_whtasapp && $validate_email) {
                            Broadcast::create([
                                'name' => $name,
                                'whatsapp' => $whatsapp,
                                'email' => $email,
                                'status_whatsapp' => Broadcast::$whatsapp_not_sent,
                                'status_email' => Broadcast::$email_not_sent,
                            ]);
                            $this->successCount = $this->successCount + 1;
                        }
                    }
                }
            }
        } else {
            $this->message[] = [
                'field' => 'Excel',
                'message' => 'Excel should be containt, minimum 1 row data',
            ];
        }
    }

    public function getResult() {
        return [
            'error_messages' => $this->message, //array
            'success_count' => $this->successCount, //integer
        ];
    }

    function validate($value, $name, $regex = null, $regex_message = null) {
        $key = $this->key;
        $success = true;
        $message = "";

        if($name == "Name") {
            if(!$value || $value == "" || $value == null || $value == '') {
                $success = false;
                $message = "Row ".$key.', '.$name." is required";
                $this->message[] = [
                    'field' => $name,
                    'message' => $message,
                ];
            }
        }

        if($name == "Whatsapp") {
            if($value && strlen($value) < 6) {
                $success = false;
                $message = "Row ".$key.', '.$name." min 6 characters";
                $this->message[] = [
                    'field' => $name,
                    'message' => $message,
                ];
            }
        }

        if($name == "Whatsapp") {
            if($value && strlen($value) > 15) {
                $success = false;
                $message = "Row ".$key.', '.$name." max 15 characters";
                $this->message[] = [
                    'field' => $name,
                    'message' => $message,
                ];
            }
        } else {
            if($value && strlen($value) > 50) {
                $success = false;
                $message = "Row ".$key.', '.$name." max 50 characters";
                $this->message[] = [
                    'field' => $name,
                    'message' => $message,
                ];
            }
        }

        if($regex) {
            if($value && !preg_match($regex, $value)) {
                $success = false;
                $message = "Row ".$key.', '.$name.' '.$regex_message;
                $this->message[] = [
                    'field' => $name,
                    'message' => $message,
                ];
            }
        }

        if($name == "Whatsapp") {
            if ($value) {
                $checkUnique = false;
                $checkUnique = Broadcast::where('whatsapp', $value)->first();
                if ($checkUnique) {
                    $success = false;
                    $message = "Row ".$key.', '.$name." has been taken, try another ".$name;
                    $this->message[] = [
                        'field' => $name,
                        'message' => $message,
                    ];
                }
            }
        }

        if($name == "Email") {
            if ($value) {
                $checkUnique = false;
                $checkUnique = Broadcast::where('email', $value)->first();
                if ($checkUnique) {
                    $success = false;
                    $message = "Row ".$key.', '.$name." has been taken, try another ".$name;
                    $this->message[] = [
                        'field' => $name,
                        'message' => $message,
                    ];
                }
            }
        }

        return $success;
    }
}
