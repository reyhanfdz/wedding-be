<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attender extends Model
{
    use HasFactory;

    public static $qr_url = "https://api.qrserver.com/v1/create-qr-code/?size=400x400&bgcolor=ffffff&color=77536d&qzone=1&data=";
    public static $will_attend = 1;             //attendance
    public static $will_not_attend = 2;         //attendance
    public static $comment_not_displayed = 1;   //status
    public static $comment_displayed = 2;       //status
    public static $qr_not_scaned = 1;           //status_attend
    public static $qr_scaned = 2;               //status_attend

    protected $fillable = [
        'name',
        'email',
        'participants',
        'attendance',
        'status',
        'status_attend',
        'comment',
        'link_qr',
    ];
}
