<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Attender extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'email',
        'participants',
        'attendance',
        'status',
        'status_attend',
        'comment',
    ];
}
