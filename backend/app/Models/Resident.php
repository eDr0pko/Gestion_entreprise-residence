<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Resident extends Model
{
    use HasFactory;

    protected $table = 'resident';
    protected $primaryKey = 'id_resident';
    public $timestamps = false;

    protected $fillable = [
        'email_personne',
        'adresse_logement',
    ];

    public function personne()
    {
        return $this->belongsTo(Personne::class, 'email_personne', 'email');
    }
}
