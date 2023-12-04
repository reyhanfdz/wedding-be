<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasFactory;

    public static $inactive = 1;
    public static $active = 2;
    public static $disabled = 3;

    protected $fillable = [
        'email',
        'password',
        'status',
        'token',
        'forgot_token',
        'activate_token',
        'code_no_pass',
        'valid_code_no_pass_until',
        'username',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'token',
        'forgot_token',
        'activate_token',
        'code_no_pass',
        'valid_code_no_pass_until',
    ];

    public function profile()
    {
        return $this->belongsTo('App\Models\Profile', 'id', 'user_id');
    }
}
