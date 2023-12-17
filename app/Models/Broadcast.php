<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Broadcast extends Model
{
    use HasFactory;

    public static $link_whatsapp = 'https://api.whatsapp.com/send/';
    public static $whatsapp_not_sent = 1;
    public static $whatsapp_sent = 2;
    public static $email_not_sent = 1;
    public static $email_sent = 2;

    protected $fillable = [
        'name',
        'whatsapp',
        'email',
        'status_whatsapp',
        'status_email',
    ];
}
