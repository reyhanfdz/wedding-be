<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class BlockDomain extends Model
{
    use HasFactory;

    protected $table = 'block_domains';
    protected $fillable = [
        'name'
    ];
}
