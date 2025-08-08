<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Log extends Model
{
    protected $table = 'logs';
    protected $fillable = [
        'user_id', 'action', 'message', 'ip_address', 'created_at'
    ];
    public $timestamps = false;
    const CREATED_AT = 'created_at';
}
