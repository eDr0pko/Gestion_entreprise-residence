<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Incident extends Model
{
    use HasFactory;

    protected $table = 'incident';
    protected $primaryKey = 'id';
    public $timestamps = false;

    protected $fillable = [
        'datetime',
        'object',
        'statut',
        'id_signaleur',
        'pieces_jointes',
    ];
}
